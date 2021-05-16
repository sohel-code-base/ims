<?php

namespace App\Repository;

use App\Entity\ProductSale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductSale|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSale|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSale[]    findAll()
 * @method ProductSale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSale::class);
    }

    public function getSaleRecords()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customer', 'customer');
        $qb->select('e.id AS saleId','e.saleDate AS orderDate', 'e.status', 'e.totalPrice', 'e.dueAmount');
        $qb->addSelect('customer.name AS customerName','customer.id AS customerId');
        $qb->orderBy('e.saleDate', 'DESC');
        return $qb->getQuery()->getArrayResult();

    }



}
