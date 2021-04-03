<?php

namespace App\Repository;

use App\Entity\ProductPurchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductPurchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductPurchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductPurchase[]    findAll()
 * @method ProductPurchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductPurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductPurchase::class);
    }

    public function getTotalProduct()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.product','product');
        $qb->select('product.proName AS productName');
        $qb->addSelect('e.quantity','e.purchasePrice','e.salePrice','e.purchaseDate');
        $qb->groupBy('product.id');
        $qb->addGroupBy('e.id');
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

}
