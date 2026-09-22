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

    public function getByCategoryAndTags(?int $category=null, ?array $tags=[]):array {
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
             
        return $q->getQuery()->getResult();
    }


    //    /**
    //     * @return Products[] Returns an array of Products objects
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

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
