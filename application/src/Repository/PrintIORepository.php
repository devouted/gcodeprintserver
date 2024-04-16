<?php

namespace App\Repository;

use App\Entity\PrintIO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrintIO>
 *
 * @method PrintIO|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintIO|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintIO[]    findAll()
 * @method PrintIO[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintIORepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintIO::class);
    }
    
    public function getLastEntry() {
        return $this->findOneBy(['status'=>0], ['id'=>'ASC']);
    }

    //    /**
    //     * @return PrintIO[] Returns an array of PrintIO objects
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

    //    public function findOneBySomeField($value): ?PrintIO
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

