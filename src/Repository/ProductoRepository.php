<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function findNameById(int $id)
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.nombre')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['nombre'] : null;
    }

    public function findByCategoriaAndConStock(string $categoria)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.categoria = :categoria')
            ->andWhere('p.cantidad > 0')
            ->setParameter(':categoria', $categoria)
            ->getQuery()
            ->getResult();

    }

    //    /**
    //     * @return Producto[] Returns an array of Producto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Producto
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
