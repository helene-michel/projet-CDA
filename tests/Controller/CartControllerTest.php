<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    use CreatesProductsTrait;

    public function testEmptyCartIconHasNoBadge(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('nav a.cart-btn[href="/cart"][aria-label="Mon panier, vide"]');
        self::assertSelectorNotExists('nav a.cart-btn.has-items');
        self::assertSelectorNotExists('.cart-btn__count');
        // Afficher la navbar ne doit pas démarrer de session pour un simple visiteur.
        self::assertResponseNotHasCookie('PHPSESSID');
    }

    public function testCartIconShowsTotalQuantity(): void
    {
        $client = static::createClient();
        $product = $this->createProduct();

        $this->addToCart($client, $product->getId(), quantity: 2);
        self::assertSelectorTextSame('.cart-btn__count', '2');
        self::assertSelectorExists('nav a.cart-btn.has-items[aria-label="Mon panier, 2 articles"]');

        $this->addToCart($client, $product->getId(), quantity: 1);
        self::assertSelectorTextSame('.cart-btn__count', '3');
        self::assertSelectorExists('nav a.cart-btn[aria-label="Mon panier, 3 articles"]');
    }

    public function testSingleItemLabelIsSingular(): void
    {
        $client = static::createClient();
        $product = $this->createProduct();

        $this->addToCart($client, $product->getId(), quantity: 1);

        self::assertSelectorExists('nav a.cart-btn[aria-label="Mon panier, 1 article"]');
    }

    private function addToCart(KernelBrowser $client, int $productId, int $quantity): void
    {
        $crawler = $client->request('GET', '/article/'.$productId);
        $form = $crawler->filter('form[action="/cart/add/'.$productId.'"]')->form();
        $form['taille']->select('m');
        $form['number'] = (string) $quantity;
        $client->submit($form);

        self::assertResponseRedirects('/article/'.$productId);
        $client->followRedirect();
    }
}
