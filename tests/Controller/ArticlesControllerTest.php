<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticlesControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    public function testProductPageDisplaysPriceInclVat(): void
    {
        $client = static::createClient();
        $product = $this->createProduct(priceExclVat: 89.5, vatRate: 20);

        $client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        // format_currency utilise une espace insécable fine avant "€"
        self::assertSelectorTextContains('.price-big', "107,40\u{a0}€");
        self::assertSelectorTextContains('.price-big', 'TTC');
    }

    public function testProductListJsonOnlyExposesCardFields(): void
    {
        $client = static::createClient();
        $product = $this->createProduct(priceExclVat: 10.99, vatRate: 20);

        $client->request('GET', '/articles/'.$product->getCategory()->getId().'/load');

        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertCount(1, $data);
        self::assertSame(['id', 'name', 'priceInclVat', 'imageUrl', 'sellerName'], array_keys($data[0]));
        self::assertSame(13.19, $data[0]['priceInclVat']);
        self::assertSame('Flora Lefèvre', $data[0]['sellerName']);
        self::assertStringContainsString('product-default', $data[0]['imageUrl']);
    }

    public function testProductPageShowsProductPhotos(): void
    {
        $client = static::createClient();
        $product = $this->createProduct();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->find(Product::class, $product->getId())->setImages(['robe-1.jpg', 'robe-2.jpg']);
        $em->flush();

        $client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#galleryMainImg[src="/uploads/productImages/robe-1.jpg"]');
        self::assertSelectorExists('.gallery-thumbs .thumb[data-src="/uploads/productImages/robe-2.jpg"]');
    }

    public function testProductPageWithoutPhotoStillRenders(): void
    {
        $client = static::createClient();
        $product = $this->createProduct();

        $client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#galleryMainImg[src*="product-default"]');
        self::assertSelectorNotExists('.gallery-thumbs');
    }

    public function testProductPageShowsSellerCompany(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $this->createUser('Flora', ['ROLE_SELLER']);
        $seller = (new Seller())->setCompanyName('Atelier Flora')->setDescription('Robes en lin.')->setSiret('123');
        $em->find(User::class, $user->getId())->setSeller($seller);
        $em->flush();
        $this->created[] = $seller;
        $product = $this->createProduct(owner: $user);

        $client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.creator-spotlight h3', 'Atelier Flora');
        self::assertSelectorTextContains('.creator-spotlight p', 'Robes en lin.');

        $client->request('GET', '/');
        self::assertSelectorTextContains('#ateliers', 'Atelier Flora');
    }

    public function testCategoryPageShowsCategoryName(): void
    {
        $client = static::createClient();
        $category = $this->createCategory('Jupes');

        $client->request('GET', '/articles/'.$category->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jupes');
        self::assertSelectorExists('[data-filter-target="count"]');
    }

    public function testSliderMaxIsTheHighestPriceInclVatRoundedUp(): void
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

        $names = $this->load($client, $category->getId(), ['price' => '72', 'sort' => '']);

        self::assertSame(['Robe en cuir'], $names);
    }

    public function testPriceFilterUsesPriceInclVat(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Short', 14.99, 20, category: $category);   // 17,99 € TTC
        $this->createProduct('Jupe', 16.00, 20, category: $category);    // 19,20 € TTC

        self::assertSame(['Short'], $this->load($client, $category->getId(), ['price' => '18']));
        self::assertSame(['Jupe', 'Short'], $this->load($client, $category->getId(), ['price' => '19.20']));
    }

    public function testNewestProductsFirstAndSortByPriceInclVat(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $this->createProduct('Ancien', 50, 20, category: $category);
        $this->createProduct('Nouveau', 10, 20, category: $category);

        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['sort' => '']));
        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['sort' => 'ASC']));
        self::assertSame(['Ancien', 'Nouveau'], $this->load($client, $category->getId(), ['sort' => 'DESC']));
        // Valeur inattendue : ignorée au lieu d'une erreur 500
        self::assertSame(['Nouveau', 'Ancien'], $this->load($client, $category->getId(), ['sort' => 'n\'importe quoi']));
    }

    public function testFilterOnSeveralTagsKeepsProductsHavingAllOfThem(): void
    {
        $client = static::createClient();
        $category = $this->createCategory();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $lin = (new Tag())->setName('Lin');
        $bio = (new Tag())->setName('Bio');
        $em->persist($lin);
        $em->persist($bio);
        $em->flush();
        $this->created[] = $lin;
        $this->created[] = $bio;

        $deux = $this->createProduct('Lin bio', 50, 20, category: $category);
        $un = $this->createProduct('Lin seul', 50, 20, category: $category);
        $em->find(Product::class, $deux->getId())->addTag($lin)->addTag($bio);
        $em->find(Product::class, $un->getId())->addTag($lin);
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

        return array_column(json_decode($client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR), 'name');
    }
}
