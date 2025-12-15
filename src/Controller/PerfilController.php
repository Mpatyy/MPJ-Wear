<?php

namespace App\Controller;

use App\Entity\Pedidos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\LineaPedido;


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
}
