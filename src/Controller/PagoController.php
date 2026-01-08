<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pedidos;
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

        $session  = $request->getSession();
        $pedidoId = $session->get('pedido_id'); // ← viene del checkout del carrito

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

        return $this->render('pago/pasarela.html.twig', [
            'pedido' => $pedido,
        ]);
    }

    #[Route('/pago/procesar', name: 'pago_procesar', methods: ['POST'])]
    public function procesar(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

        // -------- VALIDACIÓN TARJETA (igual que antes) --------
        $numero = $request->request->get('numero_tarjeta');
        $fecha  = $request->request->get('fecha_caducidad');
        $cvv    = $request->request->get('cvv');

        if (!preg_match('/^\d{16}$/', $numero)) {
            $this->addFlash('error', 'Número de tarjeta inválido.');
            return $this->redirectToRoute('pasarela_pago');
        }

        if (preg_match('/^\d{4}$/', $fecha)) {
            $fecha = substr($fecha, 0, 2) . '/' . substr($fecha, 2, 2);
        }

        if (!preg_match('/^\d{2}\/\d{2}$/', $fecha)) {
            $this->addFlash('error', 'Fecha inválida (usa MM/AA).');
            return $this->redirectToRoute('pasarela_pago');
        }

        [$mes, $anio] = explode('/', $fecha);
        $anio += 2000;

        $fechaTarjeta = \DateTime::createFromFormat('Y-m', "$anio-$mes");
        $fechaActual  = new \DateTime('first day of this month');

        if (!$fechaTarjeta || $fechaTarjeta < $fechaActual) {
            $this->addFlash('error', 'Tarjeta caducada.');
            return $this->redirectToRoute('pasarela_pago');
        }

        if (!preg_match('/^\d{3}$/', $cvv)) {
            $this->addFlash('error', 'CVV inválido.');
            return $this->redirectToRoute('pasarela_pago');
        }
        // -------------------------------------------------------

        // Marcar pedido como pagado (las líneas y total ya vienen del carrito/checkout)
        $pedido->setEstado('pagado');
        $em->flush();

        // Limpiar carrito y pedido_id
        $session->remove('carrito');
        $session->remove('pedido_id');

        // Enviar email de confirmación de pedido
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@mpj-wear.com', 'MPJ WEAR'))
            ->to($pedido->getUsuario()->getEmail())
            ->subject('Confirmación de pedido nº '.$pedido->getId())
            ->htmlTemplate('pedido_confirmado.html.twig')
            ->context([
                'pedido' => $pedido,
            ]);

        $mailer->send($email); // se enviará vía Mailtrap según tu MAILER_DSN [web:39]

        $this->addFlash('success', 'Pago realizado correctamente.');

        return $this->redirectToRoute('perfil');
    }
}
