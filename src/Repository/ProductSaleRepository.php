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
        $qb->select('e.id','e.saleDate AS orderDate', 'e.status', 'e.totalPrice');
        $qb->addSelect('customer.name AS customerName','customer.id AS customerId');
        $qb->orderBy('e.saleDate', 'DESC');
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
        $qb->join('e.product', 'purchaseProduct');
        $qb->join('purchaseProduct.product', 'product');
        $qb->leftJoin('purchaseProduct.power', 'power');
        $qb->select('e.quantity','e.perPcsPrice', 'e.totalPrice');
        $qb->addSelect('product.name AS productName');
        $qb->addSelect('power.watt');
        $qb->addSelect('customer.name AS customerName', 'customer.address AS customerAddress', 'customer.phone AS customerPhone');
        $qb->where('e.createdAt = :orderDate')->setParameter('orderDate', $filterBy['orderDate']);
        $qb->andWhere('customer.id = :customerId')->setParameter('customerId', $filterBy['customerId']);
        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }

}
