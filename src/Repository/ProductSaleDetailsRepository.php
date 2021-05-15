<?php

namespace App\Repository;

use App\Entity\ProductSaleDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductSaleDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSaleDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSaleDetails[]    findAll()
 * @method ProductSaleDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSaleDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSaleDetails::class);
    }

    // /**
    //  * @return ProductSaleDetails[] Returns an array of ProductSaleDetails objects
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
    public function findOneBySomeField($value): ?ProductSaleDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getProductByCustomerAndSaleDate($customerId, $saleDate)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.sale', 'sale');
        $qb->join('sale.customer', 'customer');
        $qb->join('sale.employee', 'employee');
        $qb->join('e.product', 'purchaseProduct');
        $qb->join('purchaseProduct.product', 'product');
        $qb->leftJoin('purchaseProduct.power', 'power');

        $qb->select('e.quantity', 'e.perPcsPrice', '(e.quantity * e.perPcsPrice) AS price');
        $qb->addSelect('customer.name AS customerName', 'customer.address AS customerAddress', 'customer.phone AS customerPhone');
        $qb->addSelect('employee.fullName AS employeeName', 'employee.signature AS employeeSignature');
        $qb->addSelect('sale.totalPrice', 'sale.dueAmount', 'sale.id AS saleId');

        $qb->addSelect('purchaseProduct.id AS productPurchaseId');
        $qb->addSelect('product.name AS productName');
        $qb->addSelect('power.watt');

        $qb->where('customer.id = :customerId')->setParameter('customerId', $customerId);
        $qb->andWhere('sale.saleDate = :saleDate')->setParameter('saleDate', $saleDate);

        return $qb->getQuery()->getArrayResult();
    }
}
