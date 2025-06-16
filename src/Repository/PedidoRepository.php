<?php

namespace App\Repository;

use App\Entity\Pedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pedido>
 */
class PedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
    }

    public function findPedidoByEstadoAndCliente(string $estado1, int $cliente, ?string $estado2 = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.id_cliente = :cliente')
            ->setParameter('cliente', $cliente);

        if ($estado2 !== null) {
            $qb->andWhere('p.estado IN (:estados)')
                ->setParameter('estados', [$estado1, $estado2]);
        } else {
            $qb->andWhere('p.estado = :estado1')
                ->setParameter('estado1', $estado1);
        }

        $qb->orderBy('p.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findPedidoByFechas($fechaInicio, $fechaFin): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($fechaInicio) {
            $qb->andWhere('p.fecha >= :fechaInicio')
                ->setParameter('fechaInicio', new \DateTime($fechaInicio));
        }

        if ($fechaFin) {
            $fechaFinDate = (new \DateTime($fechaFin))->modify('+1 day');
            $qb->andWhere('p.fecha <= :fechaFin')
                ->setParameter('fechaFin', $fechaFinDate);
        }

        return $qb->getQuery()->getResult();
    }

    public function findPedidosPorFechaEnvio(\DateTime $fechaEnvio): array
    {
        $fechaEnvioInicio = (clone $fechaEnvio)->setTime(0, 0, 0);
        $fechaEnvioFin = (clone $fechaEnvio)->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->where('p.fecha >= :inicio')
            ->andWhere('p.fecha <= :fin')
            ->setParameter('inicio', $fechaEnvioInicio)
            ->setParameter('fin', $fechaEnvioFin)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Pedido[] Returns an array of Pedido objects
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

    //    public function findOneBySomeField($value): ?Pedido
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
