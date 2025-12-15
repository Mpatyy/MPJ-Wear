<?php

namespace App\Controller;

use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Pedidos;
use App\Entity\LineaPedido;


class CarritoController extends AbstractController
{
    #[Route('/carrito/agregar', name: 'carrito_agregar', methods: ['POST'])]
    public function agregar(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $productoId = $request->request->get('producto_id');
        $talla = $request->request->get('talla');
        $color = $request->request->get('color');
        $cantidad = (int) $request->request->get('cantidad', 1);

        if (!$productoId || !$talla || !$color || $cantidad < 1) {
            $this->addFlash('error', 'No se pudo añadir el producto al carrito.');
            return $this->redirectToRoute('producto_listado');
        }

        $producto = $em->getRepository(Producto::class)->find($productoId);
        if (!$producto) {
            $this->addFlash('error', 'El producto seleccionado no existe.');
            return $this->redirectToRoute('producto_listado');
        }

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        // Clave única producto+talla+color
        $clave = $productoId.'_'.$talla.'_'.$color;

        if (isset($carrito[$clave])) {
            $carrito[$clave]['cantidad'] += $cantidad;
        } else {
            $carrito[$clave] = [
                'producto_id' => $productoId,
                'nombre'      => $producto->getNombre(),
                'talla'       => $talla,
                'color'       => $color,
                'cantidad'    => $cantidad,
                'precio'      => $producto->getPrecio(), // o precio especial si lo aplicas luego
            ];
        }

        $session->set('carrito', $carrito);

        $this->addFlash('success', 'Producto añadido al carrito.');

        return $this->redirectToRoute('producto_detalle', [
            'id' => $productoId,
        ]);
    }
    #[Route('/carrito', name: 'carrito_ver')]
    public function ver(Request $request): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        return $this->render('carrito.html.twig', [
            'carrito' => $carrito,
        ]);
    }
    #[Route('/carrito/eliminar/{clave}', name: 'carrito_eliminar', methods: ['POST'])]
    public function eliminar(string $clave, Request $request): Response
    {
        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (isset($carrito[$clave])) {
            unset($carrito[$clave]);
            $session->set('carrito', $carrito);
            $this->addFlash('success', 'Producto eliminado del carrito.');
        }

        return $this->redirectToRoute('carrito_ver');
    }
    #[Route('/carrito/vaciar', name: 'carrito_vaciar', methods: ['POST'])]
public function vaciar(Request $request): Response
{
    $session = $request->getSession();
    $session->remove('carrito');

    $this->addFlash('success', 'Carrito vaciado.');
    return $this->redirectToRoute('carrito_ver');
}
#[Route('/carrito/confirmar', name: 'carrito_confirmar', methods: ['POST'])]
    public function confirmar(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'Tu carrito está vacío.');
            return $this->redirectToRoute('carrito_ver');
        }

        $usuario = $this->getUser();

        // 1) Calcular total
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['cantidad'] * $item['precio'];
        }

        // 2) Crear pedido
        $pedido = new Pedidos();
        $pedido->setUsuario($usuario);
        $pedido->setFecha(new \DateTime());
        $pedido->setEstado('pendiente');
        $pedido->setTotal(number_format($total, 2, '.', ''));

        // De momento dejamos direccion y metodoPago a null
        $em->persist($pedido);

        // 3) Crear líneas de pedido
        foreach ($carrito as $item) {
            $producto = $em->getRepository(Producto::class)->find($item['producto_id']);
            if (!$producto) {
                continue;
            }

            $linea = new LineaPedido();
            $linea->setPedido($pedido);
            $linea->setProducto($producto);
            $linea->setTalla($item['talla']);
            $linea->setColor($item['color']);
            $linea->setCantidad($item['cantidad']);
            $linea->setPrecioUnitario(number_format($item['precio'], 2, '.', ''));

            $subtotal = $item['cantidad'] * $item['precio'];
            $linea->setSubtotal(number_format($subtotal, 2, '.', ''));

            $em->persist($linea);
        }

        // 4) Guardar en BD y vaciar carrito
        $em->flush();
        $session->remove('carrito');

        $this->addFlash('success', 'Pedido realizado correctamente.');

        return $this->redirectToRoute('perfil');
    }

}
