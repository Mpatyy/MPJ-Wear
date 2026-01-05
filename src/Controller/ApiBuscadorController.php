<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use App\Repository\ProductoVariacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/buscador', name: 'api_buscador')]
class ApiBuscadorController extends AbstractController
{
    private ProductosRepository $productosRepository;
    private ProductoVariacionRepository $variacionRepository;

    public function __construct(
        ProductosRepository $productosRepository,
        ProductoVariacionRepository $variacionRepository
    ) {
        $this->productosRepository = $productosRepository;
        $this->variacionRepository = $variacionRepository;
    }

    /**
     * Obtener sugerencias de autocompletado
     */
    #[Route('/sugerencias', name: 'sugerencias', methods: ['GET'])]
    public function obtenerSugerencias(Request $request): JsonResponse
    {
        $q = $request->query->get('q', '');

        if (strlen($q) < 1) {
            return $this->json(['sugerencias' => []]);
        }

        $productos = $this->productosRepository->findAll();
        
        $sugerencias = [];
        foreach ($productos as $p) {
            if (stripos($p->getNombre(), $q) !== false) {
                $sugerencias[$p->getNombre()] = true;
            }
        }

        return $this->json(['sugerencias' => array_keys($sugerencias)]);
    }

    /**
     * Obtener filtros DINÁMICOS según búsqueda
     * Si busca "Camiseta", solo muestra tallas/colores de camisetas
     */
    #[Route('/filtros', name: 'filtros', methods: ['GET'])]
    public function obtenerFiltros(Request $request): JsonResponse
    {
        $nombre = $request->query->get('nombre', '');

        // Si hay búsqueda, filtrar por nombre
        if (!empty($nombre)) {
            $productos = $this->productosRepository->createQueryBuilder('p')
                ->where('LOWER(p.nombre) LIKE LOWER(:nombre)')
                ->setParameter('nombre', '%' . $nombre . '%')
                ->getQuery()
                ->getResult();
        } else {
            $productos = $this->productosRepository->findAll();
        }

        $tallas = ['Todas'];
        $colores = ['Todos'];

        // Obtener tallas y colores SOLO de los productos encontrados
        foreach ($productos as $p) {
            $variaciones = $this->variacionRepository->findBy(['producto' => $p]);
            
            foreach ($variaciones as $v) {
                if ($v->getTalla() && !in_array($v->getTalla(), $tallas)) {
                    $tallas[] = $v->getTalla();
                }
                if ($v->getColor() && !in_array($v->getColor(), $colores)) {
                    $colores[] = $v->getColor();
                }
            }
        }

        sort($tallas);
        sort($colores);

        return $this->json([
            'tallas' => $tallas,
            'colores' => $colores,
        ]);
    }

    /**
     * Obtener productos filtrados (con variaciones)
     */
    #[Route('/productos', name: 'productos', methods: ['GET'])]
    public function obtenerProductos(Request $request): JsonResponse
    {
        $nombre = $request->query->get('nombre', '');
        $talla = $request->query->get('talla', '');
        $color = $request->query->get('color', '');
        $precioMax = $request->query->get('precio_max', null);

        // Obtener productos por nombre
        if (!empty($nombre)) {
            $todosProductos = $this->productosRepository->createQueryBuilder('p')
                ->where('LOWER(p.nombre) LIKE LOWER(:nombre)')
                ->setParameter('nombre', '%' . $nombre . '%')
                ->getQuery()
                ->getResult();
        } else {
            $todosProductos = $this->productosRepository->findAll();
        }
        
        $productosArray = [];
        $procesados = [];

        foreach ($todosProductos as $p) {
            // Obtener variaciones del producto
            $variaciones = $this->variacionRepository->findBy(['producto' => $p]);

            if (empty($variaciones)) {
                continue;
            }

            $variacionesArray = [];

            foreach ($variaciones as $v) {
                // Filtro por talla
                if (!empty($talla) && $talla !== 'Todas' && $v->getTalla() !== $talla) {
                    continue;
                }

                // Filtro por color
                if (!empty($color) && $color !== 'Todos' && $v->getColor() !== $color) {
                    continue;
                }

                // Filtro por precio
                if ($precioMax !== null && $precioMax !== '' && $p->getPrecio() > (float)$precioMax) {
                    continue;
                }

                // Solo stock disponible
                if ($v->getStock() <= 0) {
                    continue;
                }

                $variacionesArray[] = [
                    'id' => $v->getId(),
                    'talla' => $v->getTalla(),
                    'color' => $v->getColor(),
                    'stock' => $v->getStock(),
                    'imagen' => $v->getImagen() ?: $p->getImagen(),
                ];
            }

            // Si hay variaciones que cumplen los filtros
            if (!empty($variacionesArray) && !in_array($p->getId(), $procesados)) {
                $productosArray[] = [
                    'id' => $p->getId(),
                    'nombre' => $p->getNombre(),
                    'descripcion' => $p->getDescripcion(),
                    'precio' => $p->getPrecio(),
                    'imagen' => $p->getImagen(),
                    'variaciones' => $variacionesArray,
                ];
                $procesados[] = $p->getId();
            }
        }

        usort($productosArray, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

        return $this->json(['productos' => array_slice($productosArray, 0, 50)]);
    }
}
