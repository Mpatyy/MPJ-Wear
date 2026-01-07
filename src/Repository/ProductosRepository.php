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
        $texto = trim($texto);

        // Evita consultas innecesarias
        if ($texto === '' || mb_strlen($texto) < 2) {
            return [];
        }

        $qb = $this->createQueryBuilder('p');

        $qb->select('DISTINCT p.nombre')
            ->where('LOWER(p.nombre) LIKE LOWER(:texto)')
            ->setParameter('texto', '%' . $texto . '%')
            ->orderBy('p.nombre', 'ASC')
            ->setMaxResults($limite);

        $resultados = $qb->getQuery()->getArrayResult();

        // Extraer solo los nombres
        return array_map(fn ($item) => $item['nombre'], $resultados);
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

        $resultados = $qb->getQuery()->getArrayResult();

        return array_map(fn ($item) => $item['talla'], $resultados);
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

        $resultados = $qb->getQuery()->getArrayResult();

        return array_map(fn ($item) => $item['color'], $resultados);
    }

    /**
     * Buscar productos según los criterios del buscador
     * Filtra por nombre, talla, color y precio máximo (usando variaciones)
     */
    public function buscarFiltros(?string $texto, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $texto = $texto !== null ? trim($texto) : null;
        $talla = $talla !== null ? trim($talla) : null;
        $color = $color !== null ? trim($color) : null;
        $precioMax = $precioMax !== null ? trim($precioMax) : null;

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

        // ✅ HAVING: precioMax + stock sin pisarse
        $hayHaving = false;

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);

            $qb->having('MAX(COALESCE(v.precio, p.precio)) <= :precio')
                ->setParameter('precio', $precioFloat);

            $hayHaving = true;
        }

        // Solo productos con al menos una variación en stock
        if ($hayHaving) {
            $qb->andHaving('SUM(v.stock) > 0');
        } else {
            $qb->having('SUM(v.stock) > 0');
        }

        return $qb->orderBy('p.nombre', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * ✅ Útil para el buscador tipo Zara: solo texto y con límite
     * Devuelve productos con stock > 0
     */
    public function buscarRapido(string $q, int $limite = 12): array
    {
        $q = trim($q);

        if ($q === '' || mb_strlen($q) < 2) {
            return [];
        }

        // Reutilizamos buscarFiltros y recortamos
        $res = $this->buscarFiltros($q, null, null, null);

        return array_slice($res, 0, $limite);
    }

    public function buscarConCategoriaYFiltros(
        ?\App\Entity\Categoria $categoria,
        ?string $talla,
        ?string $color,
        ?string $precioMax
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->groupBy('p.id');

        if ($categoria) {
            $qb->andWhere('p.categoria = :categoria')
                ->setParameter('categoria', $categoria);
        }

        if ($talla) {
            $qb->andWhere('v.talla = :talla')
                ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('v.color = :color')
                ->setParameter('color', $color);
        }

        $hayHaving = false;

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);

            $qb->having('MAX(COALESCE(v.precio, p.precio)) <= :precio')
                ->setParameter('precio', $precioFloat);

            $hayHaving = true;
        }

        // Solo productos con stock
        if ($hayHaving) {
            $qb->andHaving('SUM(v.stock) > 0');
        } else {
            $qb->having('SUM(v.stock) > 0');
        }

        return $qb
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
