<?php

namespace App\Repository;

use App\Entity\CommandeLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CommandeLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeLine[]    findAll()
 * @method CommandeLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeLine::class);
    }

    // /**
    //  * @return CommandeLine[] Returns an array of CommandeLine objects
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
    public function findOneBySomeField($value): ?CommandeLine
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
