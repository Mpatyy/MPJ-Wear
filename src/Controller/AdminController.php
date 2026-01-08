<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pedidos;
use App\Entity\LineaPedido;
use App\Entity\Producto;
use App\Entity\Usuario;
use App\Entity\ResetPasswordRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\ProductoType;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // ------------------ DASHBOARD ------------------
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $pedidos = $this->em->getRepository(Pedidos::class)->findAll();
        $totalPedidos = count($pedidos);
        $pedidosPendientes = count(array_filter($pedidos, fn($p) => $p->getEstado() === 'pendiente'));
        $pedidosPagados = count(array_filter($pedidos, fn($p) => $p->getEstado() === 'pagado'));
        $totalUsuarios = count($this->em->getRepository(Usuario::class)->findAll());
        $totalProductos = count($this->em->getRepository(Producto::class)->findAll());
        $ingresos = array_sum(array_map(fn($p) => $p->getTotal(), $pedidos));

        $pedidosPorEstado = [
            'Pagados' => $pedidosPagados,
            'Pendientes' => $pedidosPendientes,
            'Otros' => $totalPedidos - $pedidosPagados - $pedidosPendientes
        ];

        $ingresosPorPedido = array_map(fn($p) => $p->getTotal(), $pedidos);

        return $this->render('admin/dashboard.html.twig', [
            'totalPedidos' => $totalPedidos,
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosPagados' => $pedidosPagados,
            'totalUsuarios' => $totalUsuarios,
            'totalProductos' => $totalProductos,
            'ingresos' => $ingresos,
            'pedidosPorEstado' => $pedidosPorEstado,
            'ingresosPorPedido' => $ingresosPorPedido
        ]);
    }

    // ------------------ PEDIDOS ------------------
    #[Route('/pedidos', name: 'admin_pedidos_list')]
    public function pedidos(): Response
    {
        $pedidos = $this->em->getRepository(Pedidos::class)->findBy([], ['id' => 'DESC']);
        return $this->render('admin/pedidos/list.html.twig', ['pedidos' => $pedidos]);
    }

    #[Route('/pedidos/ver/{id}', name: 'admin_pedidos_ver')]
    public function verPedido(int $id): Response
    {
        $pedido = $this->em->getRepository(Pedidos::class)->find($id);
        if (!$pedido) throw $this->createNotFoundException('Pedido no encontrado');

        $lineas = $this->em->getRepository(LineaPedido::class)->findBy(['pedido' => $pedido]);
        return $this->render('admin/pedidos/ver.html.twig', [
            'pedido' => $pedido,
            'lineas' => $lineas
        ]);
    }

    #[Route('/pedidos/cambiar-estado/{id}/{estado}', name: 'admin_pedidos_cambiar_estado')]
    public function cambiarEstadoPedido(int $id, string $estado): Response
    {
        $pedido = $this->em->getRepository(Pedidos::class)->find($id);
        if (!$pedido) throw $this->createNotFoundException('Pedido no encontrado');

        $pedido->setEstado($estado);
        $this->em->flush();

        $this->addFlash('success', "Estado del pedido #$id cambiado a $estado");
        return $this->redirectToRoute('admin_pedidos_list');
    }

    // ------------------ PRODUCTOS ------------------
    #[Route('/productos', name: 'admin_productos_list')]
    public function productos(): Response
    {
        $productos = $this->em->getRepository(Producto::class)->findAll();
        return $this->render('admin/productos/list.html.twig', ['productos' => $productos]);
    }

    #[Route('/productos/nuevo', name: 'admin_productos_nuevo')]
    public function nuevoProducto(Request $request): Response
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagen')->getData();
            if ($imagenFile) {
                $nuevoNombre = uniqid() . '.' . $imagenFile->guessExtension();
                try {
                    $imagenFile->move($this->getParameter('productos_imagenes'), $nuevoNombre);
                    $producto->setImagen($nuevoNombre);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Error al subir la imagen.');
                }
            }

            $this->em->persist($producto);
            $this->em->flush();
            $this->addFlash('success', 'Producto creado correctamente');
            return $this->redirectToRoute('admin_productos_list');
        }

        return $this->render('admin/productos/nuevo.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/productos/editar/{id}', name: 'admin_productos_editar')]
    public function editarProducto(Request $request, int $id): Response
    {
        $producto = $this->em->getRepository(Producto::class)->find($id);
        if (!$producto) throw $this->createNotFoundException('Producto no encontrado');

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagen')->getData();
            if ($imagenFile) {
                $nuevoNombre = uniqid() . '.' . $imagenFile->guessExtension();
                try {
                    $imagenFile->move($this->getParameter('productos_imagenes'), $nuevoNombre);
                    $producto->setImagen($nuevoNombre);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Error al subir la imagen.');
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Producto actualizado correctamente');
            return $this->redirectToRoute('admin_productos_list');
        }

        return $this->render('admin/productos/editar.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto
        ]);
    }

    #[Route('/productos/eliminar/{id}', name: 'admin_productos_eliminar')]
    public function eliminarProducto(int $id): Response
    {
        $producto = $this->em->getRepository(Producto::class)->find($id);
        if (!$producto) throw $this->createNotFoundException('Producto no encontrado');

        $this->em->remove($producto);
        $this->em->flush();

        $this->addFlash('success', "Producto eliminado correctamente");
        return $this->redirectToRoute('admin_productos_list');
    }

    // ------------------ USUARIOS ------------------
    #[Route('/usuarios', name: 'admin_usuarios_list')]
    public function usuarios(): Response
    {
        $usuarios = $this->em->getRepository(Usuario::class)->findAll();
        return $this->render('admin/usuarios/list.html.twig', ['usuarios' => $usuarios]);
    }

    #[Route('/usuarios/ver/{id}', name: 'admin_usuarios_ver')]
    public function verUsuario(int $id): Response
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($id);
        if (!$usuario) throw $this->createNotFoundException('Usuario no encontrado');

        return $this->render('admin/usuarios/ver.html.twig', ['usuario' => $usuario]);
    }

    #[Route('/usuarios/eliminar/{id}', name: 'admin_usuarios_eliminar')]
    public function eliminarUsuario(int $id): Response
    {
        $usuario = $this->em->getRepository(Usuario::class)->find($id);
        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        // 1️⃣ Borrar solicitudes de restablecimiento de contraseña
        $resetRequests = $this->em->getRepository(ResetPasswordRequest::class)->findBy(['user' => $usuario]);
        foreach ($resetRequests as $request) {
            $this->em->remove($request);
        }

        // 2️⃣ Borrar el usuario
        $this->em->remove($usuario);
        $this->em->flush();

        $this->addFlash('success', "Usuario eliminado correctamente");
        return $this->redirectToRoute('admin_usuarios_list');
    }

    #[Route('/estadisticas', name: 'admin_estadisticas')]
    public function estadisticas(): Response
    {
        $pedidos = $this->em->getRepository(Pedidos::class)->findAll();
        $totalPedidos = count($pedidos);
        $pedidosPendientes = count(array_filter($pedidos, fn($p) => $p->getEstado() === 'pendiente'));
        $pedidosPagados = count(array_filter($pedidos, fn($p) => $p->getEstado() === 'pagado'));
        $ingresos = array_sum(array_map(fn($p) => $p->getTotal(), $pedidos));

        return $this->render('admin/estadisticas.html.twig', [
            'ingresos' => $ingresos,
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosPagados' => $pedidosPagados
        ]);
    }
}
