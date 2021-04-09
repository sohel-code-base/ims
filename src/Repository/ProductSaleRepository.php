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
        $qb->select('e.createdAt AS orderDate', 'e.status', 'SUM(e.totalPrice) AS orderTotalPrice');
        $qb->addSelect('customer.cusName AS customerName','customer.id AS customerId');
        $qb->orderBy('e.createdAt', 'DESC');
        $qb->groupBy('customer.id');
        $qb->addGroupBy('e.createdAt');
        $results = $qb->getQuery()->getArrayResult();
//        $data = [];
//        foreach ($results as $result){
//            $orderDate = $result['orderDate']->format('d-m-Y');
//            $data[$result['customerId']]['product'][$orderDate] = [
//                'orderTotalPrice' => $result['orderTotalPrice'],
//            ];
//        }
//        dd($results);
        return $results;
    }

    public function getSalesRecordByCustomerAndDate($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customer', 'customer');
        $qb->join('e.product', 'productPurchase');
        $qb->join('productPurchase.product', 'product');
        $qb->leftJoin('e.watt', 'watt');
        $qb->select('e.quantity','e.perPcsPrice', 'e.totalPrice');
        $qb->addSelect('product.proName AS productName');
        $qb->addSelect('watt.watt');
        $qb->addSelect('customer.cusName AS customerName', 'customer.cusAddress AS customerAddress', 'customer.cusPhone AS customerPhone');
        $qb->where('e.createdAt = :orderDate')->setParameter('orderDate', $filterBy['orderDate']);
        $qb->andWhere('customer.id = :customerId')->setParameter('customerId', $filterBy['customerId']);
        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }
}
