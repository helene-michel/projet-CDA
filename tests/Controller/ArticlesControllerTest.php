<?php

namespace App\Tests\Controller;

use App\Entity\Products;
use App\Entity\Tags;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticlesControllerTest extends WebTestCase
{
    use CreatesProductsTrait;

    public function testProductPageDisplaysPriceTtc(): void
    {
        $client = static::createClient();
        $product = $this->createProduct(prixHt: 89.5, tva: 20);

        $client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        // format_currency utilise une espace insécable fine avant "€"
        self::assertSelectorTextContains('.price-big', "107,40\u{a0}€");
        self::assertSelectorTextContains('.price-big', 'TTC');
    }

    public function testProductListJsonContainsPrixTtc(): void
    {
        $client = static::createClient();
        $product = $this->createProduct(prixHt: 10.99, tva: 20);

        $client->request('GET', '/articles/'.$product->getCategory()->getId().'/load');

        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertCount(1, $data);
        self::assertSame(10.99, $data[0]['prixHt']);
        self::assertSame(13.19, $data[0]['prixTtc']);
    }

    public function testSliderMaxIsTheHighestTtcPriceRoundedUp(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Robe en cuir', 59.99, 20, category: $category); // 71,99 € TTC

        $client->request('GET', '/articles/'.$category->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[type="range"][max="72"][value="72"]');
        self::assertSelectorExists('[data-filter-max-value="72"]');
    }

    /**
     * Régression : avec le curseur au maximum (valeur envoyée par la page au chargement),
     * le produit le plus cher était exclu ("<" strict et prix tronqué en entier).
     */
    public function testMostExpensiveProductIsListedWhenSliderIsAtMax(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Robe en cuir', 59.99, 20, category: $category);

        $names = $this->load($client, $category->getId(), ['prix' => '72', 'tri' => '']);

        self::assertSame(['Robe en cuir'], $names);
    }

    public function testPriceFilterUsesTtcPrice(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Short', 14.99, 20, category: $category);   // 17,99 € TTC
        $this->createProduct('Jupe', 16.00, 20, category: $category);    // 19,20 € TTC

        self::assertSame(['Short'], $this->load($client, $category->getId(), ['prix' => '18']));
        self::assertSame(['Jupe', 'Short'], $this->load($client, $category->getId(), ['prix' => '19.20']));
    }

    public function testNewestProductsFirstAndSortByTtcPrice(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Ancien', 50, 20, category: $category);
        $this->createProduct('Nouveau', 10, 20, category: $category);

        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['tri' => '']));
        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['tri' => 'ASC']));
        self::assertSame(['Ancien', 'Nouveau'], $this->load($client, $category->getId(), ['tri' => 'DESC']));
        // Valeur inattendue : ignorée au lieu d'une erreur 500
        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['tri' => 'n\'importe quoi']));
    }

    public function testFilterOnSeveralTagsKeepsProductsHavingAllOfThem(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $lin = (new Tags())->setNom('Lin');
        $bio = (new Tags())->setNom('Bio');
        $em->persist($lin);
        $em->persist($bio);
        $em->flush();
        $this->created[] = $lin;
        $this->created[] = $bio;

        $deux = $this->createProduct('Lin bio', 50, 20, category: $category);
        $un = $this->createProduct('Lin seul', 50, 20, category: $category);
        $em->find(Products::class, $deux->getId())->addTag($lin)->addTag($bio);
        $em->find(Products::class, $un->getId())->addTag($lin);
        $em->flush();

        // Deux tags : erreur 500 avant la correction (même alias de jointure réutilisé)
        self::assertSame(['Lin bio'], $this->load($client, $category->getId(), ['tags' => [$lin->getId(), $bio->getId()]]));
        self::assertSame(['Lin seul', 'Lin bio'], $this->load($client, $category->getId(), ['tags' => [$lin->getId()]]));
    }

    /** @return string[] noms des produits renvoyés par l'endpoint de la liste */
    private function load(KernelBrowser $client, int $categoryId, array $query = []): array
    {
        $client->request('GET', '/articles/'.$categoryId.'/load', $query);
        self::assertResponseIsSuccessful();

        return array_column(json_decode($client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR), 'nom');
    }
}
