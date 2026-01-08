<?php

namespace App\Repository;

use App\Entity\Producto;
use App\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function listarTarjetasPorColor(?Categoria $categoria = null, ?string $color = null, ?string $precioMax = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->select('p AS producto')
            ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
            ->addSelect('MAX(v.color) AS color')
            ->addSelect('MAX(v.imagen) AS imagenColor')
            ->addSelect('SUM(v.stock) AS stockColor')
            ->where('p.activo = 1')
            ->andWhere('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''");

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
               ->setParameter('cat', $categoria);
        }

        if ($color !== null && trim($color) !== '') {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
               ->setParameter('color', $color);
        }

        if ($precioMax !== null && trim($precioMax) !== '') {
            $precioMaxNorm = str_replace(',', '.', trim($precioMax));
            if (is_numeric($precioMaxNorm)) {
                $qb->andWhere('p.precio <= :precio')
                   ->setParameter('precio', (float) $precioMaxNorm);
            }
        }

        $qb->groupBy('p.id, colorKey')
           ->having('SUM(v.stock) > 0')
           ->orderBy('p.id', 'DESC')
           ->addOrderBy('colorKey', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function obtenerSugerencias(string $texto, int $limite = 8): array
    {
        $texto = trim($texto);
        if ($texto === '' || mb_strlen($texto) < 1) return [];

        if ($limite < 1) $limite = 8;
        if ($limite > 20) $limite = 20;

        $textoLower = mb_strtolower($texto);

        $qb = $this->createQueryBuilder('p');

        $qb->select('DISTINCT p.nombre AS nombre')
            ->where('p.activo = 1')
            ->andWhere('LOWER(TRIM(p.nombre)) LIKE :prefijo')
            ->setParameter('prefijo', $textoLower . '%')
            ->addOrderBy("CASE WHEN LOWER(TRIM(p.nombre)) = :exacto THEN 0 ELSE 1 END", 'ASC')
            ->setParameter('exacto', $textoLower)
            ->addOrderBy('p.nombre', 'ASC')
            ->setMaxResults($limite);

        $resultados = $qb->getQuery()->getArrayResult();
        return array_map(static fn ($row) => $row['nombre'], $resultados);
    }

    public function obtenerTallasUnicas(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT v.talla')
            ->innerJoin('p.variaciones', 'v')
            ->where('p.activo = 1')
            ->andWhere('v.talla IS NOT NULL')
            ->andWhere("TRIM(v.talla) <> ''")
            ->orderBy('v.talla', 'ASC');

        $resultados = $qb->getQuery()->getArrayResult();
        return array_map(fn ($item) => $item['talla'], $resultados);
    }

    public function obtenerColoresUnicos(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT v.color')
            ->innerJoin('p.variaciones', 'v')
            ->where('p.activo = 1')
            ->andWhere('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''")
            ->orderBy('v.color', 'ASC');

        $resultados = $qb->getQuery()->getArrayResult();
        return array_map(fn ($item) => $item['color'], $resultados);
    }

    public function buscarConCategoriaYFiltros(?Categoria $categoria, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $talla = $talla !== null ? trim($talla) : null;
        $color = $color !== null ? trim($color) : null;
        $precioMax = $precioMax !== null ? trim($precioMax) : null;

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->where('p.activo = 1')
            ->groupBy('p.id');

        if ($categoria) {
            $qb->andWhere('p.categoria = :categoria')
               ->setParameter('categoria', $categoria);
        }

        if ($talla) {
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
               ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
               ->setParameter('color', $color);
        }

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);
            $qb->andWhere('p.precio <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        $qb->having('SUM(v.stock) > 0');

        return $qb->orderBy('p.id', 'DESC')->getQuery()->getResult();
    }

    public function buscarFiltros(?string $texto, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $texto = $texto !== null ? trim($texto) : null;
        $talla = $talla !== null ? trim($talla) : null;
        $color = $color !== null ? trim($color) : null;
        $precioMax = $precioMax !== null ? trim($precioMax) : null;

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->where('p.activo = 1')
            ->groupBy('p.id');

        if ($texto) {
            $qb->andWhere('LOWER(p.nombre) LIKE LOWER(:texto) OR LOWER(p.descripcion) LIKE LOWER(:texto)')
               ->setParameter('texto', '%' . $texto . '%');
        }

        if ($talla) {
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
               ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
               ->setParameter('color', $color);
        }

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);
            $qb->andWhere('p.precio <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        $qb->having('SUM(v.stock) > 0');

        return $qb->orderBy('p.nombre', 'ASC')->setMaxResults(50)->getQuery()->getResult();
    }

    public function buscarRapido(string $q, int $limite = 12): array
    {
        $q = trim($q);
        if ($q === '' || mb_strlen($q) < 2) return [];

        $res = $this->buscarFiltros($q, null, null, null);
        return array_slice($res, 0, $limite);
    }

    public function listarTarjetasPorColorConFiltros(?Categoria $categoria, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $talla = $talla !== null ? trim($talla) : null;
        $color = $color !== null ? trim($color) : null;
        $precioMax = $precioMax !== null ? trim($precioMax) : null;

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->select('p AS producto')
            ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
            ->addSelect('MAX(v.color) AS color')
            ->addSelect('MAX(v.imagen) AS imagenColor')
            ->addSelect('SUM(v.stock) AS stockColor')
            ->where('p.activo = 1')
            ->andWhere('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''");

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
               ->setParameter('cat', $categoria);
        }

        if ($talla) {
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
               ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
               ->setParameter('color', $color);
        }

        $qb->groupBy('p.id, colorKey')
           ->having('SUM(v.stock) > 0');

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);
            $qb->andWhere('p.precio <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        return $qb->orderBy('p.id', 'DESC')->addOrderBy('colorKey', 'ASC')->getQuery()->getArrayResult();
    }

    public function listarSimilaresPorColor(Producto $producto, int $limite = 12): array
    {
        $categoria = $producto->getCategoria();

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->select('p AS producto')
            ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
            ->addSelect('MAX(v.color) AS color')
            ->addSelect('MAX(v.imagen) AS imagenColor')
            ->addSelect('SUM(v.stock) AS stockColor')
            ->where('p.activo = 1')
            ->andWhere('p != :actual')
            ->setParameter('actual', $producto)
            ->andWhere('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''")
            ->groupBy('p.id, colorKey')
            ->having('SUM(v.stock) > 0')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limite);

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
               ->setParameter('cat', $categoria);
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function buscarSimilares(Producto $producto, int $limite = 6): array
    {
        $categoria = $producto->getCategoria();

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->select('p AS producto')
            ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
            ->addSelect('MAX(v.color) AS color')
            ->addSelect('MAX(v.imagen) AS imagenColor')
            ->addSelect('SUM(v.stock) AS stockColor')
            ->where('p.activo = 1')
            ->andWhere('p != :actual')
            ->setParameter('actual', $producto)
            ->andWhere('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''")
            ->groupBy('p.id, colorKey')
            ->having('SUM(v.stock) > 0')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limite);

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
               ->setParameter('cat', $categoria);
        }

        $res = $qb->getQuery()->getArrayResult();

        if (count($res) === 0) {
            $qb2 = $this->createQueryBuilder('p')
                ->innerJoin('p.variaciones', 'v')
                ->select('p AS producto')
                ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
                ->addSelect('MAX(v.color) AS color')
                ->addSelect('MAX(v.imagen) AS imagenColor')
                ->addSelect('SUM(v.stock) AS stockColor')
                ->where('p.activo = 1')
                ->andWhere('p != :actual')
                ->setParameter('actual', $producto)
                ->andWhere('v.color IS NOT NULL')
                ->andWhere("TRIM(v.color) <> ''")
                ->groupBy('p.id, colorKey')
                ->having('SUM(v.stock) > 0')
                ->orderBy('p.id', 'DESC')
                ->setMaxResults($limite);

            $res = $qb2->getQuery()->getArrayResult();
        }

        return $res;
    }
}
