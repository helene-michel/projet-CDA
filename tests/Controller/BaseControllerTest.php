<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    public function testHomeLinksToCategories(): void
    {
        $client = static::createClient();
        $this->createCategory('Jupes');

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('a.btn-primary-b[href="#categories"]');
        self::assertSelectorExists('#categories');
    }

    public function testSellerDashboard(): void
    {
        $client = static::createClient();
        $seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $product = $this->createProduct('Robe', 50, 20, $seller);
        $client->loginUser($seller);

        $client->request('GET', '/seller/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.results-count', '1 produit en vente');

        $client->request('GET', '/seller/products/'.$product->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Robe');
    }
}
