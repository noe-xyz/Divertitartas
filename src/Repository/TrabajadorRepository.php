<?php

namespace App\Repository;

use App\Entity\Trabajador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trabajador>
 */
class TrabajadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trabajador::class);
    }

    public function findTrabajadorByNombreYCorreo(?string $nombreCompleto, ?string $email): array
    {
        $qb = $this->createQueryBuilder('t');

        if ($nombreCompleto) {
            $qb->andWhere('LOWER(t.nombreCompleto) LIKE :nombreCompleto')
                ->setParameter('nombreCompleto', '%' . strtolower($nombreCompleto) . '%');
        }

        if ($email) {
            $qb->andWhere('LOWER(t.email) LIKE :email')
                ->setParameter('email', '%' . strtolower($email) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Trabajador[] Returns an array of Trabajador objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trabajador
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
