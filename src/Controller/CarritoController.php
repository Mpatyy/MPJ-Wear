<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\ProductoVariacion;
use App\Entity\Pedidos;
use App\Entity\LineaPedido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarritoController extends AbstractController
{
    #[Route('/carrito/agregar', name: 'carrito_agregar', methods: ['POST'])]
    public function agregar(Request $request, EntityManagerInterface $em): Response
    {
        $productoId = $request->request->get('producto_id');
        $talla      = $request->request->get('talla');
        $color      = $request->request->get('color');
        $cantidad   = (int) $request->request->get('cantidad', 1);

        if (!$productoId || !$talla || !$color || $cantidad < 1) {
            $this->addFlash('error', 'Datos incorrectos.');
            return $this->redirectToRoute('producto_listado');
        }

        $producto = $em->getRepository(Producto::class)->find($productoId);
        if (!$producto) {
            $this->addFlash('error', 'Producto no encontrado.');
            return $this->redirectToRoute('producto_listado');
        }

        $variacion = $em->getRepository(ProductoVariacion::class)->findOneBy([
            'producto' => $producto,
            'talla'    => $talla,
            'color'    => $color,
        ]);

        if (!$variacion) {
            $this->addFlash('error', 'Variación no disponible.');
            return $this->redirectToRoute('producto_detalle', ['id' => $productoId]);
        }

        $imagen = $variacion->getImagen(); 

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        $clave = $productoId . '_' . $talla . '_' . $color;

        if (isset($carrito[$clave])) {
            $carrito[$clave]['cantidad'] += $cantidad;
        } else {
            $carrito[$clave] = [
                'producto_id' => $productoId,
                'nombre'      => $producto->getNombre(),
                'talla'       => $talla,
                'color'       => $color,
                'cantidad'    => $cantidad,
                'precio'      => $producto->getPrecio(),
                'imagen'      => $imagen, 
            ];
        }

        $session->set('carrito', $carrito);
        $this->addFlash('success', 'Producto añadido al carrito.');

        return $this->redirectToRoute('carrito_ver');
    }

    #[Route('/carrito', name: 'carrito_ver')]
    public function ver(Request $request): Response
    {
        return $this->render('carrito.html.twig', [
            'carrito' => $request->getSession()->get('carrito', [])
        ]);
    }

    #[Route('/carrito/eliminar/{clave}', name: 'carrito_eliminar', methods: ['POST'])]
    public function eliminar(string $clave, Request $request): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        unset($carrito[$clave]);

        $session->set('carrito', $carrito);
        $this->addFlash('success', 'Producto eliminado.');

        return $this->redirectToRoute('carrito_ver');
    }

    #[Route('/carrito/vaciar', name: 'carrito_vaciar', methods: ['POST'])]
    public function vaciar(Request $request): Response
    {
        $request->getSession()->remove('carrito');
        $this->addFlash('success', 'Carrito vaciado.');

        return $this->redirectToRoute('carrito_ver');
    }
}
