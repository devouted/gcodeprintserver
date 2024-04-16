<?php

namespace App\Repository;

use App\Entity\PrintJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrintJob>
 *
 * @method PrintJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintJob[]    findAll()
 * @method PrintJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintJob::class);
    }

    //    /**
    //     * @return PrintJob[] Returns an array of PrintJob objects
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

    //    public function findOneBySomeField($value): ?PrintJob
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
