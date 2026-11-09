<?php

namespace App\Tests\Controller\DashboardSeller;

use App\Entity\Product;
use App\Tests\Controller\CreatesProductTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsControllerSellerTest extends WebTestCase
{
    use CreatesProductTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexListsOnlyTheSellerOwnProducts(): void
    {
        $seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $other = $this->createUser('Camille', ['ROLE_SELLER']);
        $this->createProduct('Robe de Flora', 76, 20, $seller);
        $this->createProduct('Jupe de Camille', 50, 20, $other);
        $this->client->loginUser($seller);

        $this->client->request('GET', '/seller/products');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame('Mes produits | Le comptoire des couturiers');
        self::assertSelectorTextContains('.results-count', '1 produit en vente');
        self::assertSelectorTextContains('.seller-products-table', 'Robe de Flora');
        self::assertSelectorTextNotContains('.seller-products-table', 'Jupe de Camille');
        // 76 € HT, TVA 20 % => 91,20 € TTC
        self::assertSelectorTextContains('.price-ttc', "91,20\u{a0}€");
        self::assertSelectorExists('a.seller-add[href="/seller/products/new"]');
        self::assertSelectorExists('a[aria-label="Modifier « Robe de Flora »"]');
        self::assertSelectorExists('button.delete[aria-label="Supprimer « Robe de Flora »"]');
    }

    public function testIndexShowsEmptyState(): void
    {
        $this->client->loginUser($this->createUser('Flora', ['ROLE_SELLER']));

        $this->client->request('GET', '/seller/products');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.seller-empty h2', "Vous n'avez pas encore de produit");
        self::assertSelectorNotExists('table');
    }

    public function testSellerCannotManageAnotherSellerProduct(): void
    {
        $seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $product = $this->createProduct('Jupe de Camille', 50, 20, $this->createUser('Camille', ['ROLE_SELLER']));
        $this->client->loginUser($seller);

        $this->client->request('GET', '/seller/products/'.$product->getId());
        self::assertResponseStatusCodeSame(403);
        $this->client->request('GET', '/seller/products/'.$product->getId().'/edit');
        self::assertResponseStatusCodeSame(403);
        $this->client->request('POST', '/seller/products/'.$product->getId(), ['_token' => 'peu importe']);
        self::assertResponseStatusCodeSame(403);
    }

    public function testSellerCanOpenOwnProductPages(): void
    {
        $seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $product = $this->createProduct('Robe de Flora', 76, 20, $seller);
        $this->client->loginUser($seller);

        $this->client->request('GET', '/seller/products/'.$product->getId());
        self::assertResponseIsSuccessful();
        $this->client->request('GET', '/seller/products/'.$product->getId().'/edit');
        self::assertResponseIsSuccessful();
    }

    public function testSellerCanDeleteOwnProductFromTheList(): void
    {
        $seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $product = $this->createProduct('Robe de Flora', 76, 20, $seller);
        $id = $product->getId();
        $this->client->loginUser($seller);

        $crawler = $this->client->request('GET', '/seller/products');
        $this->client->submit($crawler->filter('form[action="/seller/products/'.$id.'"]')->form());

        self::assertResponseRedirects('/seller/products');
        $this->client->followRedirect();
        self::assertSelectorExists('.seller-empty');
        self::assertNull(static::getContainer()->get(EntityManagerInterface::class)->find(Product::class, $id));
    }
}
