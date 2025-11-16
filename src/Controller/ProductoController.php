<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Producto;

class ProductoController extends AbstractController
{
    #[Route('/productos', name: 'producto_listado')]
    public function listado(EntityManagerInterface $entityManager): Response
    {
        $productos = $entityManager->getRepository(Producto::class)->findAll();
        return $this->render('producto/listado.html.twig', ['productos' => $productos]);
    }

    #[Route('/producto/{id}', name: 'producto_detalle')]
    public function detalle(EntityManagerInterface $entityManager, int $id): Response
    {
        $producto = $entityManager->getRepository(Producto::class)->find($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }
        $variaciones = $producto->getVariaciones(); // Obtener coleccion de variaciones

        return $this->render('producto/detalle.html.twig', [
            'producto' => $producto,
            'variaciones' => $variaciones,
        ]);
    }
}
