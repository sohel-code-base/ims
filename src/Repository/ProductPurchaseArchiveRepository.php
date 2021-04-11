<?php

namespace App\Repository;

use App\Entity\ProductPurchaseArchive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductPurchaseArchive|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductPurchaseArchive|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductPurchaseArchive[]    findAll()
 * @method ProductPurchaseArchive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductPurchaseArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductPurchaseArchive::class);
    }

    // /**
    //  * @return ProductPurchaseArchive[] Returns an array of ProductPurchaseArchive objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductPurchaseArchive
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
