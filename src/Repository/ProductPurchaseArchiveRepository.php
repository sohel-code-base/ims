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

    public function getProductPurchaseByMonth($filterBy)
    {
        $beginningOfMonth = $filterBy->format('Y-m-01');
        $endOfMonth = $filterBy->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.product', 'product');
        $qb->leftJoin('e.power', 'power');

        $qb->select('product.proName AS productName');
        $qb->addSelect('power.watt');
        $qb->addSelect('e.quantity', 'e.purchasePrice','e.status', 'e.purchaseDate');

        $qb->where('e.purchaseDate >= :beginningOfMonth')->setParameter('beginningOfMonth', $beginningOfMonth);
        $qb->andWhere('e.purchaseDate <= :endOfMonth')->setParameter('endOfMonth', $endOfMonth);
        $qb->orderBy('e.purchaseDate', 'DESC');
        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }
}
