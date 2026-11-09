<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeoControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testRobotsTxt(): void
    {
        $this->client->request('GET', '/robots.txt');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/plain; charset=UTF-8');
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('Disallow: /admin/', $content);
        self::assertStringContainsString('Sitemap: http://localhost/sitemap.xml', $content);
    }

    public function testSitemapListsHomeCategoriesAndProducts(): void
    {
        $product = $this->createProduct();

        $this->client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($this->client->getResponse()->getContent());
        self::assertNotFalse($xml, 'Le sitemap doit être un XML valide.');
        $locs = array_map('strval', $xml->xpath('//*[local-name()="loc"]'));
        self::assertContains('http://localhost/', $locs);
        self::assertContains('http://localhost/articles/'.$product->getCategory()->getId(), $locs);
        self::assertContains('http://localhost/article/'.$product->getId(), $locs);
    }

    public function testHomePageHasSeoTagsAndFavicons(): void
    {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame('Créations cousues main par des créatrices françaises | Le comptoire des couturiers');
        self::assertSelectorExists('html[lang="fr"]');
        self::assertSelectorExists('meta[name="description"][content]');
        self::assertSelectorExists('meta[name="robots"][content="index, follow"]');
        self::assertSelectorExists('link[rel="canonical"][href="http://localhost/"]');
        self::assertSelectorExists('meta[property="og:image"][content="http://localhost/icon-512.png"]');
        self::assertSelectorExists('link[rel="icon"][href="/favicon.ico"]');
        self::assertSelectorExists('link[rel="apple-touch-icon"]');
        self::assertSelectorExists('link[rel="manifest"]');
        self::assertSelectorExists('script[type="application/ld+json"]');
    }

    public function testProductPageHasProductMetadata(): void
    {
        $product = $this->createProduct();

        $crawler = $this->client->request('GET', '/article/'.$product->getId());

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame('Robe "Camille" & co | Le comptoire des couturiers');
        self::assertSelectorExists('meta[property="og:type"][content="product"]');
        $description = $crawler->filter('meta[name="description"]')->attr('content');
        self::assertSame(155, mb_strlen($description));
        self::assertStringEndsWith('…', $description);

        $jsonLd = json_decode($crawler->filter('script[type="application/ld+json"]')->text(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertSame('Product', $jsonLd['@type']);
        self::assertSame('Robe "Camille" & co', $jsonLd['name']);
        self::assertSame(107.4, $jsonLd['offers']['price']); // 89,50 € HT + 20 %
        self::assertSame('EUR', $jsonLd['offers']['priceCurrency']);
    }

    public function testPrivatePagesAreNotIndexed(): void
    {
        $this->client->request('GET', '/login');

        self::assertPageTitleSame('Connexion | Le comptoire des couturiers');
        self::assertSelectorExists('meta[name="robots"][content="noindex, follow"]');
    }
}
