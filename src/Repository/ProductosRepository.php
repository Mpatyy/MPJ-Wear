<?php 

namespace App\Repository;

use App\Bridge\Doctrine\Orm\AbstractRepository;
use App\Entity\Productos; 
use Doctrine\Persistence\ManagerRegistry;


class ProductosRepository extends AbstractRepository
{

    public function buscarFiltros(?string $texto, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $qb = $this->createQueryBuilder('p');

        // Filtro por Nombre (Buscador principal)
        if ($texto) {
            $qb->andWhere('p.nombre LIKE :texto OR p.descripcion LIKE :texto')
               ->setParameter('texto', '%' . $texto . '%');
        }

        // Filtro por Talla (Exacta)
        if ($talla) {
            $qb->andWhere('p.talla = :talla')
               ->setParameter('talla', $talla);
        }

        // Filtro por Color (Exacto)
        if ($color) {
            $qb->andWhere('p.color = :color')
               ->setParameter('color', $color);
        }

        // Filtro por Precio (Muestra productos que cuesten MENOS o IGUAL a lo seleccionado)
        if ($precioMax) {
            $precioFloat = (float) $precioMax;
            $qb->andWhere('p.precio <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        // Ordenar por nombre para que quede ordenado y limitar resultados (autocompletado)
        return $qb->orderBy('p.nombre', 'ASC')
                     ->setMaxResults(20)
                     ->getQuery()
                     ->getResult();
    }
}