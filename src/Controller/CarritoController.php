<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\ProductoVariacion;
use App\Entity\Pedidos;
use App\Entity\LineaPedido;
use App\Entity\Direccion;
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

    #[Route('/carrito/checkout', name: 'carrito_checkout')]
    public function checkout(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'El carrito está vacío.');
            return $this->redirectToRoute('carrito_ver');
        }

/** @var \App\Entity\Usuario $usuario */
    $usuario     = $this->getUser();
    $direcciones = $usuario->getDirecciones();

    // GET: mostrar carrito + direcciones
    if ($request->isMethod('GET')) {
        return $this->render('carrito_checkout.html.twig', [
            'carrito'     => $carrito,
            'direcciones' => $direcciones,
        ]);
    }

    // POST: seleccionar o crear dirección
    $direccionId  = $request->request->get('direccion_id');
    $nuevaCalle   = trim((string) $request->request->get('nueva_calle'));

    if (!$direccionId && $nuevaCalle !== '') {
        $direccion = new Direccion();
        $direccion->setUsuario($usuario);
        $direccion->setCalle($nuevaCalle);
        $direccion->setCiudad($request->request->get('nueva_ciudad'));
        $direccion->setCp($request->request->get('nueva_cp'));
        $direccion->setProvincia($request->request->get('nueva_provincia'));
        $direccion->setPais($request->request->get('nueva_pais'));
        $direccion->setTipo('envio');

        $em->persist($direccion);
        $em->flush(); // para que tenga id

        $direccionId = $direccion->getId();
    }

    if (!$direccionId) {
        $this->addFlash('error', 'Debes seleccionar o crear una dirección.');
        return $this->redirectToRoute('carrito_checkout');
    }

    $direccion = $em->getRepository(Direccion::class)->find($direccionId);
    if (!$direccion || $direccion->getUsuario()->getId() !== $usuario->getId()) {
        $this->addFlash('error', 'Dirección no válida.');
        return $this->redirectToRoute('carrito_checkout');
    }

    // Calcular total
    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['cantidad'] * $item['precio'];
    }

    // Crear pedido pendiente de pago
    $pedido = new Pedidos();
    $pedido->setUsuario($usuario);
    $pedido->setFecha(new \DateTime());
    $pedido->setEstado('pendiente_pago');
    $pedido->setTotal(number_format($total, 2, '.', ''));
    $pedido->setDireccion($direccion);

    $em->persist($pedido);

    // Crear líneas de pedido
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
        $linea->setPrecioUnitario($item['precio']);
        $linea->setSubtotal($item['cantidad'] * $item['precio']);
        $linea->setImagen($item['imagen']);

        $em->persist($linea);
    }

    $em->flush();

    $session->set('pedido_id', $pedido->getId());

    return $this->redirectToRoute('pasarela_pago');
}
}
