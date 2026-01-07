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

    #[Route('/sugerencias', name: 'sugerencias', methods: ['GET'])]
    public function obtenerSugerencias(Request $request): JsonResponse
    {
        $q = trim((string) $request->query->get('q', ''));

        if (mb_strlen($q) < 1) {
            return $this->json(['sugerencias' => []]);
        }

        $limite = (int) $request->query->get('limite', 8);
        if ($limite <= 0) $limite = 8;
        if ($limite > 20) $limite = 20;

        $sugerencias = $this->productosRepository->obtenerSugerencias($q, $limite);

        return $this->json(['sugerencias' => $sugerencias]);
    }

    #[Route('/productos', name: 'productos', methods: ['GET'])]
    public function obtenerProductos(Request $request): JsonResponse
    {
        $nombre = trim((string) $request->query->get('nombre', ''));
        $talla  = trim((string) $request->query->get('talla', ''));
        $color  = trim((string) $request->query->get('color', ''));
        $precioMaxRaw = $request->query->get('precio_max', null);

        // ✅ si no hay nombre, devolvemos vacío (para no listar todo al abrir)
        if ($nombre === '') {
            return $this->json(['productos' => []]);
        }

        $tallaFiltro = ($talla !== '' && $talla !== 'Todas') ? $talla : null;
        $colorFiltro = ($color !== '' && $color !== 'Todos') ? $color : null;

        $precioMax = null;
        if ($precioMaxRaw !== null) {
            $p = str_replace(',', '.', trim((string) $precioMaxRaw));
            if ($p !== '' && is_numeric($p)) $precioMax = (float) $p;
        }

        // ✅ empieza por (NO contiene)
        $todosProductos = $this->productosRepository->createQueryBuilder('p')
            ->where('LOWER(p.nombre) LIKE LOWER(:nombre)')
            ->setParameter('nombre', $nombre . '%')
            ->orderBy('p.nombre', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        $productosArray = [];

        foreach ($todosProductos as $p) {

            if ($precioMax !== null && (float) $p->getPrecio() > $precioMax) {
                continue;
            }

            $variaciones = $this->variacionRepository->findBy(['producto' => $p]);
            if (!$variaciones) continue;

            $variacionesFiltradas = [];

            foreach ($variaciones as $v) {
                if ((int) $v->getStock() <= 0) continue;

                $tVar = trim((string) $v->getTalla());
                $cVar = trim((string) $v->getColor());

                if ($tallaFiltro !== null && $tVar !== $tallaFiltro) continue;
                if ($colorFiltro !== null && $cVar !== $colorFiltro) continue;

                $imgVar = $v->getImagen() ?: $p->getImagen();

                $variacionesFiltradas[] = [
                    'id'    => $v->getId(),
                    'talla' => $v->getTalla(),
                    'color' => $v->getColor(),
                    'stock' => $v->getStock(),
                    'imagen'=> $imgVar ? '/images/' . $imgVar : null,
                ];
            }

            if (count($variacionesFiltradas) === 0) continue;

            // ✅ imagen principal del resultado = primera variación filtrada si hay filtros
            $imgPrincipal = $p->getImagen();
            if ($tallaFiltro !== null || $colorFiltro !== null) {
                $imgPrincipal = $variacionesFiltradas[0]['imagen'] ?? null;
            } else {
                $imgPrincipal = $imgPrincipal ? '/images/' . $imgPrincipal : null;
            }

            $productosArray[] = [
                'id'          => $p->getId(),
                'nombre'      => $p->getNombre(),
                'descripcion' => $p->getDescripcion(),
                'precio'      => $p->getPrecio(),
                'imagen'      => $imgPrincipal,
                'url'         => '/productos/' . $p->getId(),
                'variaciones' => $variacionesFiltradas,
            ];
        }

        return $this->json(['productos' => $productosArray]);
    }
}
