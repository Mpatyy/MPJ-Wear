<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pedidos;
use App\Entity\Producto;
use App\Entity\LineaPedido;
use App\Entity\ProductoVariacion;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PagoController extends AbstractController
{
    #[Route('/pago', name: 'pasarela_pago')]
    public function pasarela(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'No hay productos en el carrito.');
            return $this->redirectToRoute('carrito_ver');
        }

        return $this->render('pago/pasarela.html.twig');
    }

    #[Route('/pago/procesar', name: 'pago_procesar', methods: ['POST'])]
    public function procesar(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'El carrito está vacío.');
            return $this->redirectToRoute('carrito_ver');
        }

        $usuario = $this->getUser();
        $total = 0;

        foreach ($carrito as $item) {
            $total += $item['cantidad'] * $item['precio'];
        }

        $pedido = new Pedidos();
        $pedido->setUsuario($usuario);
        $pedido->setFecha(new \DateTime());
        $pedido->setEstado('pagado');
        $pedido->setTotal(number_format($total, 2, '.', ''));

        $em->persist($pedido);

        foreach ($carrito as $item) {

            $producto = $em->getRepository(Producto::class)->find($item['producto_id']);
            if (!$producto) continue;

            $variacion = $em->getRepository(ProductoVariacion::class)->findOneBy([
                'producto' => $item['producto_id'],
                'talla'    => $item['talla'],
                'color'    => $item['color']
            ]);

            if ($variacion) {
                $nuevoStock = $variacion->getStock() - $item['cantidad'];

                if ($nuevoStock < 0) {
                    $this->addFlash('error', 'No hay stock suficiente para '.$item['nombre']);
                    return $this->redirectToRoute('carrito_ver');
                }

                $variacion->setStock($nuevoStock);
                $em->persist($variacion);
            }

            $linea = new LineaPedido();
            $linea->setPedido($pedido);
            $linea->setProducto($producto);
            $linea->setTalla($item['talla']);
            $linea->setColor($item['color']);
            $linea->setCantidad($item['cantidad']);
            $linea->setPrecioUnitario($item['precio']);
            $linea->setSubtotal($item['cantidad'] * $item['precio']);

            $em->persist($linea);
        }

        $em->flush();
        $session->remove('carrito');

        $this->addFlash('success', 'Pago realizado correctamente.');

        return $this->redirectToRoute('perfil');
    }
}
