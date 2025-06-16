<?php

namespace App\Repository;

use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cliente>
 */

#Donde se hacen todas las queries a la base de datos, a la tabla de Cliente
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    public function findClienteByNombreYCorreo(?string $nombreCompleto, ?string $email): array
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

    public function findAllActivos(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.eliminado = :eliminado')
            ->setParameter('eliminado', 0)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Cliente[] Returns an array of Cliente objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cliente
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
