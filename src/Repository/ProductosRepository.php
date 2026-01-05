<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    /**
     * Obtener sugerencias de autocompletado por nombre
     */
    public function obtenerSugerencias(string $texto, int $limite = 8): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb->where('LOWER(p.nombre) LIKE LOWER(:texto)')
           ->setParameter('texto', '%' . $texto . '%')
           ->select('DISTINCT p.nombre')
           ->setMaxResults($limite)
           ->orderBy('p.nombre', 'ASC');

        $resultados = $qb->getQuery()->getResult();
        
        // Extraer solo los nombres
        return array_map(fn($item) => $item['nombre'], $resultados);
    }

    /**
     * Obtener tallas únicas disponibles desde producto_variacion
     */
    public function obtenerTallasUnicas(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT v.talla')
            ->innerJoin('p.variaciones', 'v')
            ->where('v.talla IS NOT NULL')
            ->orderBy('v.talla', 'ASC');

        $resultados = $qb->getQuery()->getResult();

        return array_map(fn($item) => $item['talla'], $resultados);
    }

    /**
     * Obtener colores únicos disponibles desde producto_variacion
     */
    public function obtenerColoresUnicos(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT v.color')
            ->innerJoin('p.variaciones', 'v')
            ->where('v.color IS NOT NULL')
            ->orderBy('v.color', 'ASC');

        $resultados = $qb->getQuery()->getResult();

        return array_map(fn($item) => $item['color'], $resultados);
    }

    /**
     * Buscar productos según los criterios del buscador
     * Filtra por nombre, talla, color y precio máximo (usando variaciones)
     */
    public function buscarFiltros(?string $texto, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->groupBy('p.id');

        if ($texto) {
            $qb->andWhere('LOWER(p.nombre) LIKE LOWER(:texto) OR LOWER(p.descripcion) LIKE LOWER(:texto)')
               ->setParameter('texto', '%' . $texto . '%');
        }

        if ($talla) {
            $qb->andWhere('v.talla = :talla')
               ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('v.color = :color')
               ->setParameter('color', $color);
        }

        if ($precioMax) {
            $precioFloat = (float) $precioMax;
            $qb->having('MAX(COALESCE(v.precio, p.precio)) <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        // Solo productos con al menos una variación en stock
        $qb->having('SUM(v.stock) > 0');

        return $qb->orderBy('p.nombre', 'ASC')
                  ->setMaxResults(50)
                  ->getQuery()
                  ->getResult();
    }
}
