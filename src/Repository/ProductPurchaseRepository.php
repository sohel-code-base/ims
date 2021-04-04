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
        $qb->join('product.proCategory','category');
        $qb->join('category.subCategories','subCat');
        $qb->select('product.proName AS productName');
        $qb->addSelect('category.catName');
        $qb->addSelect('subCat.subCatName');
        $qb->addSelect('e.id','e.quantity','e.purchasePrice','e.salePrice','e.purchaseDate');
        $qb->groupBy('product.id');
        $qb->addGroupBy('category.id');
        $qb->addGroupBy('subCat.id');
        $qb->addGroupBy('e.id');
        $results = $qb->getQuery()->getArrayResult();
        $items = [];
        foreach ($results as $result){
            $items[$result['productName']] = $result;
        }
//        dd($items);
        return $items;
    }

}
