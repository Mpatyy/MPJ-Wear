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
    public function listado(Request $request, EntityManagerInterface $entityManager, ProductosRepository $repo): Response
    {
    // ✅ Categoría (limpia)
    $slug = trim((string) $request->query->get('categoria', ''));
    $slug = $slug === '' ? null : $slug;

    // ✅ Filtros (limpios)
    $talla     = trim((string) $request->query->get('talla', ''));
    $color     = trim((string) $request->query->get('color', ''));
    $precioMax = trim((string) $request->query->get('precioMax', ''));

    $talla     = $talla === '' ? null : $talla;
    $color     = $color === '' ? null : $color;
    $precioMax = $precioMax === '' ? null : $precioMax;

    $categorias = $entityManager->getRepository(Categoria::class)
        ->findBy([], ['nombre' => 'ASC']);

    $categoriaActiva = null;
    if ($slug) {
        $categoriaActiva = $entityManager
            ->getRepository(Categoria::class)
            ->findOneBy(['slug' => $slug]);
    }

    // ✅ Si el slug no existe, lo tratamos como "ver todo"
    if ($slug && !$categoriaActiva) {
        $slug = null;
        $categoriaActiva = null;
    }

    // ✅ Hay filtros SOLO si hay valores reales
    $hayFiltros = ($talla !== null) || ($color !== null) || ($precioMax !== null);

    // ✅ Modo tarjetas si:
    // - no hay filtros (ver todo)
    // - o hay filtro por COLOR (con o sin talla)
    $modoTarjetas = (!$hayFiltros) || ($color !== null);

    $productos = [];
    $tarjetas  = [];

    if ($modoTarjetas) {
        if ($hayFiltros) {
            // 🔥 Con filtros y color -> tarjetas filtradas (para que salga la imagen del color)
            $tarjetas = $repo->listarTarjetasPorColorConFiltros(
                $categoriaActiva,
                $talla,
                $color,
                $precioMax
            );
        } else {
            // 🔥 Sin filtros -> ver todo por color
            $tarjetas = $repo->listarTarjetasPorColor($categoriaActiva);
        }
    } else {
        // 🔹 Sin color (ej: solo talla, o talla+precio) -> productos normales
        $productos = $repo->buscarConCategoriaYFiltros(
            $categoriaActiva,
            $talla,
            $color,
            $precioMax
        );
    }

    $tallasDisponibles  = $repo->obtenerTallasUnicas();
    $coloresDisponibles = $repo->obtenerColoresUnicos();

    return $this->render('producto/listado.html.twig', [
        'productos'          => $productos,
        'tarjetas'           => $tarjetas,
        'modoTarjetas'       => $modoTarjetas,
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
    public function detalle(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $producto = $em->getRepository(Producto::class)->find($id);

        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $variaciones = $em->getRepository(ProductoVariacion::class)->findBy(['producto' => $id]);

        // ✅ Color que viene desde el listado: /productos/1?color=Azul%20marino
        $colorUrl = trim((string) $request->query->get('color', ''));
        $colorUrl = $colorUrl === '' ? null : $colorUrl;

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

        // ✅ Imagen inicial: primera variación con imagen, si no la del producto
        $imagenInicial = $producto->getImagen();
        foreach ($variaciones as $v) {
            if ($v->getImagen()) {
                $imagenInicial = $v->getImagen();
                break;
            }
        }

        // ✅ Colores únicos (cada color con su imagen y su hex opcional)
        $colores = [];
        foreach ($variaciones as $v) {
            $colorVar = $v->getColor();
            if (!$colorVar) continue;

            $key = mb_strtolower(trim($colorVar));

            if (!isset($colores[$key])) {
                $colores[$key] = [
                    'color' => $colorVar,
                    'imagen' => $v->getImagen(),
                    'hex' => $mapaHex[$key] ?? null,
                ];
            }
        }

        return $this->render('producto/detalle.html.twig', [
            'producto'      => $producto,
            'variaciones'   => $variaciones,
            'colores'       => array_values($colores),
            'imagenInicial' => $imagenInicial,
            'colorUrl'      => $colorUrl,
        ]);
    }
}
