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

    /**
     * ✅ Ver todo: tarjetas por COLOR (Producto + Color)
     * Opcional: filtrar por categoria, color y precioMax (p.precio).
     *
     * Devuelve array para Twig:
     * [
     *   ['producto' => Producto, 'color' => 'Negro', 'imagenColor' => 'camiseta-negra.png', 'stockColor' => 25],
     *   ...
     * ]
     */
    public function listarTarjetasPorColor(
        ?Categoria $categoria = null,
        ?string $color = null,
        ?string $precioMax = null
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->select('p AS producto')
            ->addSelect('LOWER(TRIM(v.color)) AS colorKey')
            ->addSelect('MAX(v.color) AS color')
            ->addSelect('MAX(v.imagen) AS imagenColor')
            ->addSelect('SUM(v.stock) AS stockColor')
            ->where('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''");

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
            ->setParameter('cat', $categoria);
        }

        // ✅ Si viene color, filtramos normalizado (ignora mayúsculas y espacios)
        if ($color !== null && trim($color) !== '') {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
            ->setParameter('color', $color);
        }

        // ✅ precioMax con p.precio (tu BD)
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

        return array_map(fn ($item) => $item['nombre'], $resultados);
    }

    public function obtenerTallasUnicas(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT v.talla')
            ->innerJoin('p.variaciones', 'v')
            ->where('v.talla IS NOT NULL')
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
            ->where('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''")
            ->orderBy('v.color', 'ASC');

        $resultados = $qb->getQuery()->getArrayResult();

        return array_map(fn ($item) => $item['color'], $resultados);
    }

    /**
     * ✅ Buscar con categoria + filtros (color/talla/precioMax)
     * - color y talla comparan normalizado: LOWER(TRIM())
     * - precioMax filtra con p.precio (en tu BD el precio está en productos)
     * - stock > 0 SOLO sobre variaciones que cumplen filtros
     */
    public function buscarConCategoriaYFiltros(
        ?Categoria $categoria,
        ?string $talla,
        ?string $color,
        ?string $precioMax
    ): array {
        $talla = $talla !== null ? trim($talla) : null;
        $color = $color !== null ? trim($color) : null;
        $precioMax = $precioMax !== null ? trim($precioMax) : null;

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.variaciones', 'v')
            ->groupBy('p.id');

        if ($categoria) {
            $qb->andWhere('p.categoria = :categoria')
            ->setParameter('categoria', $categoria);
        }

        // ✅ TALLA (robusto: ignora mayúsculas y espacios)
        if ($talla) {
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
            ->setParameter('talla', $talla);
        }

        // ✅ COLOR (robusto: ignora mayúsculas y espacios)
        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
            ->setParameter('color', $color);
        }

        $hayHaving = false;

        // ✅ PRECIO MAX (si tu variación tiene precio, lo usa; si no, usa el de producto)
        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);

            $qb->having('MAX(COALESCE(v.precio, p.precio)) <= :precio')
            ->setParameter('precio', $precioFloat);

            $hayHaving = true;
        }

        // ✅ Stock > 0 (IMPORTANTE: con talla+color, SUM(v.stock) ya será del “mismo v” filtrado)
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


    /**
     * ✅ Buscar filtros general (si lo usas en buscador)
     * Lo dejo funcionando igual, pero arreglando:
     * - comparaciones normalizadas
     * - precioMax con p.precio
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
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
            ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
            ->setParameter('color', $color);
        }

        $hayHaving = false;

        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);

            $qb->having('MAX(COALESCE(v.precio, p.precio)) <= :precio')
            ->setParameter('precio', $precioFloat);

            $hayHaving = true;
        }

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


    public function buscarRapido(string $q, int $limite = 12): array
    {
        $q = trim($q);

        if ($q === '' || mb_strlen($q) < 2) {
            return [];
        }

        $res = $this->buscarFiltros($q, null, null, null);

        return array_slice($res, 0, $limite);
    }

    /**
     * ✅ Tarjetas por COLOR aplicando filtros (color/talla/precio/categoría)
     * Devuelve array para Twig:
     * [
     *   ['producto' => Producto, 'colorKey' => 'blanco', 'color' => 'Blanco', 'imagenColor' => 'xxx.png', 'stockColor' => 10],
     * ]
     */
    public function listarTarjetasPorColorConFiltros(
        ?Categoria $categoria,
        ?string $talla,
        ?string $color,
        ?string $precioMax
    ): array {
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
            ->where('v.color IS NOT NULL')
            ->andWhere("TRIM(v.color) <> ''");

        if ($categoria) {
            $qb->andWhere('p.categoria = :cat')
            ->setParameter('cat', $categoria);
        }

        // ✅ Si filtras por TALLA, que sea en la MISMA variación
        if ($talla) {
            $qb->andWhere('LOWER(TRIM(v.talla)) = LOWER(TRIM(:talla))')
            ->setParameter('talla', $talla);
        }

        // ✅ Si filtras por COLOR, que sea en la MISMA variación
        if ($color) {
            $qb->andWhere('LOWER(TRIM(v.color)) = LOWER(TRIM(:color))')
            ->setParameter('color', $color);
        }

        // ✅ Group para tarjeta por producto+color
        $qb->groupBy('p.id, colorKey');

        // ✅ Stock > 0
        $qb->having('SUM(v.stock) > 0');

        // ✅ Precio máximo (si lo usas): ojo, aquí no queremos “MAX de todos los colores”,
        // queremos el máximo del conjunto filtrado (ya filtrado por color/talla arriba)
        if ($precioMax !== null && $precioMax !== '' && is_numeric(str_replace(',', '.', $precioMax))) {
            $precioFloat = (float) str_replace(',', '.', $precioMax);

            $qb->andHaving('MAX(COALESCE(v.precio, p.precio)) <= :precio')
            ->setParameter('precio', $precioFloat);
        }

        return $qb
            ->orderBy('p.id', 'DESC')
            ->addOrderBy('colorKey', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
