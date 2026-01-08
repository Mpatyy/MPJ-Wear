<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pedidos;
use App\Entity\LineaPedido;
use App\Entity\Producto;
use App\Entity\ProductoVariacion;
use App\Entity\Usuario;
use App\Entity\ResetPasswordRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        return $this->render('admin/dashboard.html.twig', [
            'totalPedidos' => $totalPedidos,
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosPagados' => $pedidosPagados,
            'totalUsuarios' => $totalUsuarios,
            'totalProductos' => $totalProductos,
            'ingresos' => $ingresos,
            'pedidosPorEstado' => $pedidosPorEstado
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
        $productos = $this->em->getRepository(Producto::class)->findBy(
            ['activo' => true],
            ['id' => 'DESC']
        );

        return $this->render('admin/productos/list.html.twig', ['productos' => $productos]);
    }


#[Route('/productos/nuevo', name: 'admin_productos_nuevo')]
public function nuevoProducto(Request $request, EntityManagerInterface $em)
{
    $producto = new Producto();

    $form = $this->createForm(ProductoType::class, $producto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Imagen principal
        $imagenFile = $form->get('imagen')->getData();
        if ($imagenFile) {
            $newFilename = uniqid('prod_') . '.png';
            $imagenFile->move($this->getParameter('productos_directory'), $newFilename);
            $producto->setImagen($newFilename);
        }

        // Variaciones
        foreach ($producto->getVariaciones() as $key => $variacion) {
            $imagenVarFile = $form->get('variaciones')->get($key)->get('imagen')->getData();
            if ($imagenVarFile) {
                $newFilenameVar = uniqid('var_') . '.png';
                $imagenVarFile->move($this->getParameter('productos_directory'), $newFilenameVar);
                $variacion->setImagen($newFilenameVar);
            }
        }

        $em->persist($producto);
        $em->flush();

        $this->addFlash('success', 'Producto creado correctamente');
        return $this->redirectToRoute('admin_productos_list');
    }

    return $this->render('admin/productos/nuevo.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/productos/editar/{id}', name: 'admin_productos_editar')]
public function editarProducto(Request $request, Producto $producto, EntityManagerInterface $em)
{
    if ($producto->getVariaciones()->isEmpty()) {
        $producto->addVariacion(new ProductoVariacion());
    }

    $form = $this->createForm(ProductoType::class, $producto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Imagen principal
        $imagenFile = $form->get('imagen')->getData();
        if ($imagenFile) {
            $newFilename = uniqid() . '.' . $imagenFile->guessExtension();
            $imagenFile->move($this->getParameter('productos_directory'), $newFilename);
            $producto->setImagen($newFilename);
        }

        // Variaciones
        foreach ($producto->getVariaciones() as $key => $variacion) {
            $imagenVarFile = $form->get('variaciones')->get($key)->get('imagen')->getData();
            if ($imagenVarFile) {
                $newFilenameVar = uniqid() . '.' . $imagenVarFile->guessExtension();
                $imagenVarFile->move($this->getParameter('productos_directory'), $newFilenameVar);
                $variacion->setImagen($newFilenameVar);
            }
        }

        $em->persist($producto);
        $em->flush();

        $this->addFlash('success', 'Producto actualizado correctamente');
        return $this->redirectToRoute('admin_productos_list');
    }

    return $this->render('admin/productos/editar.html.twig', [
        'producto' => $producto,
        'form' => $form->createView(),
    ]);
}


    #[Route('/productos/eliminar/{id}', name: 'admin_productos_eliminar')]
    public function eliminarProducto(int $id): Response
    {
        $producto = $this->em->getRepository(Producto::class)->find($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $producto->setActivo(false);
        $this->em->flush();

        $this->addFlash('success', 'Producto desactivado correctamente');
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
        if (!$usuario) throw $this->createNotFoundException('Usuario no encontrado');

        $resetRequests = $this->em->getRepository(ResetPasswordRequest::class)->findBy(['user' => $usuario]);
        foreach ($resetRequests as $request) {
            $this->em->remove($request);
        }

        $this->em->remove($usuario);
        $this->em->flush();

        $this->addFlash('success', "Usuario eliminado correctamente");
        return $this->redirectToRoute('admin_usuarios_list');
    }

    // ------------------ ESTADÍSTICAS ------------------
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
