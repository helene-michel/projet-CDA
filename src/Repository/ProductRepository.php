<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    private const PRICE_INCL_VAT = 'p.priceExclVat * (1 + p.vatRate / 100)';

    /**
     * @param int[]|string[] $tags      product must have ALL of them
     * @param string|null    $priceOrder "ASC" / "DESC", newest first otherwise
     *
     * @return Product[]
     */
    public function getByCategoryAndTags(?int $category = null, ?array $tags = [], ?float $maxPriceInclVat = null, ?string $priceOrder = null): array
    {
        $q = $this->createQueryBuilder('p')
            ->addSelect(self::PRICE_INCL_VAT.' AS HIDDEN price_incl_vat');

        // One join alias per tag
        foreach (array_values($tags ?? []) as $i => $tag) {
            $q->innerJoin('p.tags', 't'.$i, 'WITH', 't'.$i.'.id = :tag'.$i)
                ->setParameter('tag'.$i, $tag);
        }

        if ($category) {
            $q->andWhere('p.category = :cat')
                ->setParameter('cat', $category);
        }

        if (null !== $maxPriceInclVat) {
            // <= and +0.005: compare to the displayed (rounded) price
            $q->andWhere(self::PRICE_INCL_VAT.' <= :price')
                ->setParameter('price', $maxPriceInclVat + 0.005);
        }

        $direction = strtoupper((string) $priceOrder);
        if (\in_array($direction, ['ASC', 'DESC'], true)) {
            $q->orderBy('price_incl_vat', $direction);
        } else {
            $q->orderBy('p.id', 'DESC');
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Prix TTC maximum de la catégorie, arrondi à l'euro supérieur (borne haute du curseur, pas de 1 €).
     */
    public function priceMax(Category $category): ?int
    {
        $max = $this->createQueryBuilder('p')
            ->select('MAX('.self::PRICE_INCL_VAT.')')
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $category)
            ->getQuery()
            ->getSingleScalarResult();

        return null === $max ? null : (int) ceil(round((float) $max, 2));
    }

    /** @return Product[] same category, newest first */
    public function findSimilar(Product $product, int $limit = 4): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->andWhere('p != :product')
            ->setParameter('category', $product->getCategory())
            ->setParameter('product', $product)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Identifiants uniquement (sitemap), sans hydrater les entités.
     *
     * @return int[]
     */
    public function findAllIds(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }
}
