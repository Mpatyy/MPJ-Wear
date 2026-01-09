<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pedidos;
use App\Entity\Tarjeta;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class PagoController extends AbstractController
{
    #[Route('/pago', name: 'pasarela_pago')]
    public function pasarela(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $session  = $request->getSession();
        $pedidoId = $session->get('pedido_id');

        if (!$pedidoId) {
            $this->addFlash('error', 'No hay ningún pedido pendiente de pago.');
            return $this->redirectToRoute('carrito_ver');
        }

        $pedido = $em->getRepository(Pedidos::class)->find($pedidoId);
        if (!$pedido) {
            $this->addFlash('error', 'El pedido no existe.');
            $session->remove('pedido_id');
            return $this->redirectToRoute('carrito_ver');
        }

        // ✅ CLAVE: cargar desde BD para que siempre aparezcan las últimas tarjetas guardadas
        $tarjetas = $em->getRepository(Tarjeta::class)->findBy(
            ['user' => $usuario],
            ['id' => 'DESC']
        );

        return $this->render('pago/pasarela.html.twig', [
            'pedido'   => $pedido,
            'tarjetas' => $tarjetas,
        ]);
    }

    #[Route('/pago/procesar', name: 'pago_procesar', methods: ['POST'])]
    public function procesar(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $session  = $request->getSession();
        $pedidoId = $session->get('pedido_id');

        if (!$pedidoId) {
            $this->addFlash('error', 'No hay ningún pedido pendiente de pago.');
            return $this->redirectToRoute('carrito_ver');
        }

        $pedido = $em->getRepository(Pedidos::class)->find($pedidoId);
        if (!$pedido) {
            $this->addFlash('error', 'El pedido no existe.');
            $session->remove('pedido_id');
            return $this->redirectToRoute('carrito_ver');
        }

        // ✅ Si eliges tarjeta guardada
        $tarjetaGuardada = (string) $request->request->get('tarjeta_guardada', 'nueva');

        $numero = '';
        $fecha  = '';
        $cvv    = '';

        if ($tarjetaGuardada !== 'nueva') {
            $tarjeta = $em->getRepository(Tarjeta::class)->find((int) $tarjetaGuardada);

            if (!$tarjeta || $tarjeta->getUser()?->getId() !== $usuario->getId()) {
                $this->addFlash('error', 'Tarjeta guardada no válida.');
                return $this->redirectToRoute('pasarela_pago');
            }

            // Usamos datos guardados (si quieres pedir CVV siempre, dímelo y lo ajusto)
            $numero = (string) $tarjeta->getNumero();
            $fecha  = $tarjeta->getCaducidad()?->format('m/y') ?? '';
            $cvv    = (string) $tarjeta->getCvv();
        } else {
            // ✅ Tarjeta nueva (aceptar ambos nombres por si twig usa "numero" o "numero_tarjeta")
            $numero = (string) ($request->request->get('numero_tarjeta') ?? $request->request->get('numero') ?? '');
            $fecha  = (string) ($request->request->get('fecha_caducidad') ?? '');
            $cvv    = (string) ($request->request->get('cvv') ?? '');
        }

        // ✅ Limpiar número (espacios/guiones)
        $numero = preg_replace('/\D+/', '', $numero);

        if (!preg_match('/^\d{16}$/', $numero)) {
            $this->addFlash('error', 'Número de tarjeta inválido.');
            return $this->redirectToRoute('pasarela_pago');
        }

        // ✅ Fecha puede venir como "YYYY-MM" o "MM/AA" o "MMYY"
        $fecha = trim($fecha);

        if (preg_match('/^\d{4}\-\d{2}$/', $fecha)) {
            $anio = (int) substr($fecha, 0, 4);
            $mes  = (int) substr($fecha, 5, 2);
            $fecha = sprintf('%02d/%02d', $mes, $anio % 100);
        } elseif (preg_match('/^\d{4}$/', $fecha)) {
            $fecha = substr($fecha, 0, 2) . '/' . substr($fecha, 2, 2);
        }

        if (!preg_match('/^\d{2}\/\d{2}$/', $fecha)) {
            $this->addFlash('error', 'Fecha inválida (usa MM/AA).');
            return $this->redirectToRoute('pasarela_pago');
        }

        [$mes, $anio2] = explode('/', $fecha);
        $mes  = (int) $mes;
        $anio = 2000 + (int) $anio2;

        if ($mes < 1 || $mes > 12) {
            $this->addFlash('error', 'Mes inválido.');
            return $this->redirectToRoute('pasarela_pago');
        }

        $fechaTarjeta = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $anio, $mes));
        $fechaTarjeta?->setTime(0, 0, 0);
        $fechaActual  = new \DateTime('first day of this month');
        $fechaActual->setTime(0, 0, 0);

        if (!$fechaTarjeta || $fechaTarjeta < $fechaActual) {
            $this->addFlash('error', 'Tarjeta caducada.');
            return $this->redirectToRoute('pasarela_pago');
        }

        $cvv = preg_replace('/\D+/', '', $cvv);
        if (!preg_match('/^\d{3}$/', $cvv)) {
            $this->addFlash('error', 'CVV inválido.');
            return $this->redirectToRoute('pasarela_pago');
        }

        // ✅ Guardar tarjeta si checkbox marcado (SOLO si es tarjeta nueva)
        $guardar = (string) $request->request->get('guardar_tarjeta', '0');
        if ($tarjetaGuardada === 'nueva' && $guardar === '1') {
            // Evitar duplicados: misma tarjeta para el mismo usuario
            $existente = $em->getRepository(Tarjeta::class)->findOneBy([
                'user'   => $usuario,
                'numero' => $numero,
            ]);

            if (!$existente) {
                $t = new Tarjeta();
                $t->setUser($usuario);
                $t->setNumero($numero);
                $t->setCaducidad($fechaTarjeta);
                $t->setCvv($cvv);

                $em->persist($t);
                // no flush todavía, lo hacemos al final
            }
        }

        // ✅ Pago OK
        $pedido->setEstado('pagado');
        $em->flush();

        // Limpiar carrito y pedido_id
        $session->remove('carrito');
        $session->remove('pedido_id');

        // Email confirmación
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@mpj-wear.com', 'MPJ WEAR'))
            ->to($pedido->getUsuario()->getEmail())
            ->subject('Confirmación de pedido nº ' . $pedido->getId())
            ->htmlTemplate('pedido_confirmado.html.twig')
            ->context(['pedido' => $pedido]);

        $mailer->send($email);

        $this->addFlash('success', 'Pago realizado correctamente.');
        return $this->redirectToRoute('perfil');
    }
}
