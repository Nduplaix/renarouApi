<?php

namespace App\Repository;

use App\Entity\CommandeStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CommandeStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeStatus[]    findAll()
 * @method CommandeStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeStatus::class);
    }

    // /**
    //  * @return CommandeStatus[] Returns an array of CommandeStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommandeStatus
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
