<?php

namespace App\Controller;

use App\Entity\Pedidos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\LineaPedido;
use App\Entity\Direccion;
use Symfony\Component\HttpFoundation\Request;


use App\Repository\DireccionRepository;


class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'perfil')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $usuario = $this->getUser();

        // Pedidos del usuario ordenados del más reciente al más antiguo
        $pedidos = $em->getRepository(Pedidos::class)->findBy(
            ['usuario' => $usuario],
            ['fecha' => 'DESC']
        ); // findBy con criteria + order está recomendado por Doctrine para este tipo de listados [web:274][web:272]

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
        $calle     = $request->request->get('calle');
        $ciudad    = $request->request->get('ciudad');
        $cp        = $request->request->get('cp');
        $provincia = $request->request->get('provincia');
        $pais      = $request->request->get('pais');
        $tipo      = $request->request->get('tipo');

        $dir = new Direccion();
        $dir->setUsuario($usuario);
        $dir->setCalle($calle);
        $dir->setCiudad($ciudad);
        $dir->setCp($cp);
        $dir->setProvincia($provincia);
        $dir->setPais($pais);
        $dir->setTipo($tipo);

        $em->persist($dir);
        $em->flush();

        return $this->redirectToRoute('mis_direcciones');
    }

    return $this->render('nueva_direccion.html.twig');
}


}
