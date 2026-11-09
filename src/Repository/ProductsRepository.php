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

    /** Prix TTC calculé en DQL (même formule que PriceCalculator). */
    private const PRIX_TTC = 'p.prix_ht * (1 + p.TVA / 100)';

    /**
     * Produits d'une catégorie pour la liste du front (contrôleur Stimulus "filter").
     *
     * @param int[]|string[] $tags       le produit doit avoir TOUS ces tags
     * @param float|null     $maxPriceTtc prix TTC maximum, inclus (curseur de prix)
     * @param string|null    $priceOrder  "ASC" / "DESC" (tri par prix TTC), sinon les plus récents d'abord
     *
     * @return Products[]
     */
    public function getByCategoryAndTags(?int $category = null, ?array $tags = [], ?float $maxPriceTtc = null, ?string $priceOrder = null): array
    {
        $q = $this->createQueryBuilder('p')
            ->addSelect(self::PRIX_TTC.' AS HIDDEN prix_ttc');

        // Une jointure (alias + paramètre) distincte par tag : réutiliser le même alias plante la requête.
        foreach (array_values($tags ?? []) as $i => $tag) {
            $q->innerJoin('p.Tags', 't'.$i, 'WITH', 't'.$i.'.id = :tag'.$i)
                ->setParameter('tag'.$i, $tag);
        }

        if ($category) {
            $q->andWhere('p.category = :cat')
                ->setParameter('cat', $category);
        }

        if (null !== $maxPriceTtc) {
            // <= : le produit le plus cher doit rester visible quand le curseur est au maximum.
            // +0.005 : on compare au prix affiché, arrondi au centime.
            $q->andWhere(self::PRIX_TTC.' <= :price')
                ->setParameter('price', $maxPriceTtc + 0.005);
        }

        $direction = strtoupper((string) $priceOrder);
        if (\in_array($direction, ['ASC', 'DESC'], true)) {
            $q->orderBy('prix_ttc', $direction);
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
            ->select('MAX('.self::PRIX_TTC.')')
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $category)
            ->getQuery()
            ->getSingleScalarResult();

        return null === $max ? null : (int) ceil(round((float) $max, 2));
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
