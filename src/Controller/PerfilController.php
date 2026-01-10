<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Entity\LineaPedido;
use App\Entity\Direccion;
use App\Entity\Tarjeta;
use App\Form\TarjetaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'perfil')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $usuario = $this->getUser();
        $pedidos = $em->getRepository(Pedidos::class)->findBy(
            ['usuario' => $usuario],
            ['fecha' => 'DESC']
        );

        return $this->render('perfil.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/perfil/pedido/{id}', name: 'perfil_pedido_detalle')]
    public function pedidoDetalle(int $id, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $usuario = $this->getUser();

        // 1) Buscar el pedido y asegurar que pertenece al usuario logueado
        $pedido = $em->getRepository(Pedidos::class)->find($id);

        if (!$pedido || $pedido->getUsuario() !== $usuario) {
            throw $this->createNotFoundException('Pedido no encontrado.');
        }

        // 2) Buscar líneas del pedido
        $lineas = $em->getRepository(LineaPedido::class)->findBy(
            ['pedido' => $pedido]
        ); // findBy con criteria es la forma estándar de traer relaciones simples [web:274][web:272]

        return $this->render('pedido_detalle.html.twig', [
            'pedido' => $pedido,
            'lineas' => $lineas,
        ]);
    }

    #[Route('/perfil/direcciones', name: 'mis_direcciones')]
    public function misDirecciones(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();

        $direcciones = $em->getRepository(Direccion::class)->findBy(
            ['usuario' => $usuario],
            ['id' => 'DESC']
        );

        return $this->render('mis_direcciones.html.twig', [
            'direcciones' => $direcciones,
        ]);
    }

    #[Route('/perfil/direcciones/nueva', name: 'nueva_direccion')]
    public function nuevaDireccion(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $usuario = $this->getUser();

        if ($request->isMethod('POST')) {
            $calle     = trim((string) $request->request->get('calle'));
            $ciudad    = trim((string) $request->request->get('ciudad'));
            $cp        = trim((string) $request->request->get('cp'));
            $provincia = trim((string) $request->request->get('provincia'));
            $pais      = trim((string) $request->request->get('pais'));
            $tipo      = trim((string) $request->request->get('tipo'));

            // Validaciones simples
            if ($calle === '' || $ciudad === '' || $cp === '' || $provincia === '' || $pais === '') {
                $this->addFlash('error', 'Todos los campos marcados con * son obligatorios.');
            } elseif (!preg_match('/^\d{5}$/', $cp)) {
                $this->addFlash('error', 'El código postal debe tener exactamente 5 dígitos.');
            } else {
                $dir = new Direccion();
                $dir->setUsuario($usuario);
                $dir->setCalle($calle);
                $dir->setCiudad($ciudad);
                $dir->setCp($cp);
                $dir->setProvincia($provincia);
                $dir->setPais($pais);
                $dir->setTipo($tipo); // puede ir vacío

                $em->persist($dir);
                $em->flush();

                $this->addFlash('success', 'Dirección guardada correctamente.');
                return $this->redirectToRoute('mis_direcciones');
            }
        }

        return $this->render('nueva_direccion.html.twig');
    }

    #[Route('/perfil/metodos-pago', name: 'metodos_pago')]
    public function metodosPago(EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Usuario $usuario */
        $usuario = $this->getUser();

        $tarjeta = new Tarjeta();
        $form = $this->createForm(TarjetaType::class, $tarjeta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ 1) Limpieza: guardamos SOLO dígitos en BD
            $numeroLimpio = preg_replace('/\D+/', '', (string) $tarjeta->getNumero());
            $cvvLimpio    = preg_replace('/\D+/', '', (string) $tarjeta->getCvv());

            $tarjeta->setNumero($numeroLimpio);
            $tarjeta->setCvv($cvvLimpio);

            // ✅ 2) Asignar usuario
            $tarjeta->setUser($usuario);

            // ✅ 3) Evitar duplicados (misma tarjeta + misma caducidad para ese usuario)
            $existe = $em->getRepository(Tarjeta::class)->findOneBy([
                'user'      => $usuario,
                'numero'    => $numeroLimpio,
                'caducidad' => $tarjeta->getCaducidad(),
            ]);

            if ($existe) {
                $this->addFlash('error', 'Esa tarjeta ya está guardada.');
                return $this->redirectToRoute('metodos_pago');
            }

            $em->persist($tarjeta);
            $em->flush();

            $this->addFlash('success', 'Tarjeta guardada correctamente.');
            return $this->redirectToRoute('metodos_pago');
        }

        // ✅ 4) Mostrar tarjetas ordenadas (últimas primero)
        $tarjetas = $em->getRepository(Tarjeta::class)->findBy(
            ['user' => $usuario],
            ['id' => 'DESC']
        );

        return $this->render('metodos_pago.html.twig', [
            'tarjetas' => $tarjetas,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/perfil/metodos-pago/eliminar/{id}', name: 'eliminar_tarjeta')]
    public function eliminarTarjeta(int $id, EntityManagerInterface $em): Response
    {
        $tarjeta = $em->getRepository(Tarjeta::class)->find($id);
        if ($tarjeta) {
            $em->remove($tarjeta);
            $em->flush();
        }
        return $this->redirectToRoute('metodos_pago');
    }
}
