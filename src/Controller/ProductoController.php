<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Producto;
use App\Entity\Categoria;
use App\Entity\ProductoVariacion;
use App\Repository\ProductosRepository;

class ProductoController extends AbstractController
{
    #[Route('/productos', name: 'producto_listado')]
    public function listado(Request $request, EntityManagerInterface $entityManager): Response
    {
        // 🔹 Categoría por querystring
        $slug = $request->query->get('categoria');

        // 🔹 Filtros
        $talla     = $request->query->get('talla');
        $color     = $request->query->get('color');
        $precioMax = $request->query->get('precioMax');

        $categorias = $entityManager->getRepository(Categoria::class)
            ->findBy([], ['nombre' => 'ASC']);

        $categoriaActiva = null;
        if ($slug) {
            $categoriaActiva = $entityManager
                ->getRepository(Categoria::class)
                ->findOneBy(['slug' => $slug]);
        }

        if ($slug && !$categoriaActiva) {
            $slug = null;
        }

        /** @var ProductosRepository $repo */
        $repo = $entityManager->getRepository(Producto::class);

        // 🔹 Usamos el repository con filtros
        if ($categoriaActiva || $talla || $color || $precioMax) {
            $productos = $repo->buscarConCategoriaYFiltros(
                $categoriaActiva,
                $talla,
                $color,
                $precioMax
            );
        } else {
            $productos = $repo->findBy([], ['id' => 'DESC']);
        }

        // 🔹 Datos para pintar filtros
        $tallasDisponibles  = $repo->obtenerTallasUnicas();
        $coloresDisponibles = $repo->obtenerColoresUnicos();

        return $this->render('producto/listado.html.twig', [
            'productos'          => $productos,
            'categorias'         => $categorias,
            'slugActiva'         => $slug,
            'tallasDisponibles'  => $tallasDisponibles,
            'coloresDisponibles' => $coloresDisponibles,
            'filtros' => [
                'talla'     => $talla,
                'color'     => $color,
                'precioMax' => $precioMax,
            ],
        ]);
    }

   #[Route('/productos/{id}', name: 'producto_detalle', requirements: ['id' => '\d+'])]
    public function detalle(EntityManagerInterface $em, int $id): Response
    {
        // ✅ Buscar el producto
        $producto = $em->getRepository(Producto::class)->find($id);

        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        // ✅ Buscar las variaciones del producto
        $variaciones = $em->getRepository(ProductoVariacion::class)->findBy(['producto' => $id]);

        // Mapa opcional para pintar los swatches
        $mapaHex = [
            'negro' => '#111111',
            'blanco' => '#F5F5F5',
            'gris' => '#9CA3AF',
            'azul' => '#1D4ED8',
            'azul marino' => '#0B1E3B',
            'beige' => '#D6C5A1',
            'rojo' => '#DC2626',
            'verde' => '#16A34A',
            'marron' => '#7C4A2D',
            'marrón' => '#7C4A2D',
        ];

        // Imagen inicial: primera variación con imagen, si no la del producto
        $imagenInicial = $producto->getImagen();
        foreach ($variaciones as $v) {
            if ($v->getImagen()) {
                $imagenInicial = $v->getImagen();
                break;
            }
        }

        // Colores únicos (cada color con su imagen y su hex opcional)
        $colores = [];
        foreach ($variaciones as $v) {
            $color = $v->getColor();
            if (!$color) continue;

            $key = mb_strtolower(trim($color));

            if (!isset($colores[$key])) {
                $colores[$key] = [
                    'color' => $color,
                    'imagen' => $v->getImagen(), // imagen por color
                    'hex' => $mapaHex[$key] ?? null,
                ];
            }
        }

        return $this->render('producto/detalle.html.twig', [
            'producto' => $producto,
            'variaciones' => $variaciones,
            'colores' => array_values($colores),
            'imagenInicial' => $imagenInicial,
        ]);
    }
}
