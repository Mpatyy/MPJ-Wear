<?php

namespace App\Controller\Api;

use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BuscarController extends AbstractController
{
    #[Route('/api/buscar', name: 'api_buscar', methods: ['GET'])]
    public function buscar(Request $request, ProductosRepository $repo): JsonResponse
    {
        $q = trim((string) $request->query->get('q', ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return $this->json(['resultados' => []]);
        }

        // ✅ Query directo (LIKE) usando el mismo EntityRepository
        $productos = $repo->createQueryBuilder('p')
            ->andWhere('LOWER(p.nombre) LIKE :q OR LOWER(p.descripcion) LIKE :q')
            ->setParameter('q', '%' . mb_strtolower($q) . '%')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();

        $res = [];
        foreach ($productos as $p) {
            $imagen = $p->getImagen() ? '/images/' . $p->getImagen() : null;

            $res[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'precio' => number_format((float) $p->getPrecio(), 2, ',', '.') . ' €',
                'imagen' => $imagen,
                'url' => $this->generateUrl('producto_detalle', ['id' => $p->getId()]),
            ];
        }

        return $this->json(['resultados' => $res]);
    }
}
