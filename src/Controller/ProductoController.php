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

        $productos = [];
        $tarjetas  = [];

        /**
         * ✅ CLAVE (para que salga la imagen del color filtrado):
         * - Si hay COLOR (da igual si también hay talla o precio) -> TARJETAS con imagenColor
         * - Si NO hay color y NO hay talla -> TARJETAS (ver todo / precioMax)
         * - Si hay talla pero NO hay color -> PRODUCTOS normales
         */
        $modoTarjetas = ($color !== null) || ($talla === null && $color === null);

        if ($modoTarjetas) {
            // ✅ Si hay cualquier filtro (talla/color/precio), usamos tarjetas con filtros
            if ($talla !== null || $color !== null || $precioMax !== null) {
                $tarjetas = $repo->listarTarjetasPorColorConFiltros(
                    $categoriaActiva,
                    $talla,
                    $color,
                    $precioMax
                );
            } else {
                // ✅ Ver todo sin filtros
                $tarjetas = $repo->listarTarjetasPorColor($categoriaActiva, null, null);
            }
        } else {
            // ✅ Solo talla (sin color) -> productos
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
    public function detalle(Request $request, EntityManagerInterface $em, ProductosRepository $repo, int $id): Response
    {
        $producto = $em->getRepository(Producto::class)->find($id);

        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $variaciones = $em->getRepository(ProductoVariacion::class)->findBy(['producto' => $id]);

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

        $imagenInicial = $producto->getImagen();
        foreach ($variaciones as $v) {
            if ($v->getImagen()) {
                $imagenInicial = $v->getImagen();
                break;
            }
        }

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

        // ✅ SIMILARES
        $similares = $repo->buscarSimilares($producto, 6);

        return $this->render('producto/detalle.html.twig', [
            'producto'      => $producto,
            'variaciones'   => $variaciones,
            'colores'       => array_values($colores),
            'imagenInicial' => $imagenInicial,
            'colorUrl'      => $colorUrl,
            'similares'     => $similares,
        ]);
    }
}
