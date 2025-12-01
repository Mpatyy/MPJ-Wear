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

    public function buscarFiltros(?string $texto, ?string $talla, ?string $color, ?string $precioMax): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($texto) {
            $qb->andWhere('p.nombre LIKE :texto OR p.descripcion LIKE :texto')
               ->setParameter('texto', '%' . $texto . '%');
        }

        if ($talla) {
            $qb->andWhere('p.talla = :talla')
               ->setParameter('talla', $talla);
        }

        if ($color) {
            $qb->andWhere('p.color = :color')
               ->setParameter('color', $color);
        }

        if ($precioMax) {
            $precioFloat = (float) $precioMax;
            $qb->andWhere('p.precio <= :precio')
               ->setParameter('precio', $precioFloat);
        }

        return $qb->orderBy('p.nombre', 'ASC')
                  ->setMaxResults(20)
                  ->getQuery()
                  ->getResult();
    }
}
