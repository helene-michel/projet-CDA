<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Products;
use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function getByCategoryAndTags(?int $category=null, ?array $tags=[], ?int $price=null, ?string $priceOrder=null):array {
        $q = $this->createQueryBuilder('p');

        if($tags){
            foreach($tags as $tag){
                $q
                ->innerJoin('p.Tags', 't')
                ->andWhere('t.id = :tag')
                ->setParameter('tag', $tag)
            ;
            }
            
        }

        if($category){
            $q
                ->andWhere('p.category = :cat')
                ->setParameter('cat', $category)
            ;
        }
         
        if($price){
            $q
                ->andWhere('p.prix_ht < :price')
                ->setParameter('price', $price)
            ;
        }
        if($priceOrder){
            $q
                ->orderBy('p.prix_ht', $priceOrder)
            ;
        }
        return $q->getQuery()->getResult();
    }

public function priceMax(Category $category){
    return $this->createQueryBuilder('p')
        ->select('MAX(p.prix_ht)')
        ->andWhere('p.category = :cat')
        ->setParameter('cat', $category)
        ->getQuery()
        ->getSingleScalarResult()
    ;
}
}
