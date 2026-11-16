<?php

namespace App\Tests\Controller;

use App\Entity\Seller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreatorControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    public function testNavbarLinksToCreatorList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertSelectorExists('nav a[href="/creatrices"]');
    }

    public function testListLinksToSellerPageWithProducts(): void
    {
        $client = static::createClient();
        $user = $this->createUser('Zéphyrine', ['ROLE_SELLER']);
        $seller = $this->createSeller($user, 'Atelier Zéphyr');
        $this->createProduct('Robe Camille', owner: $user);

        $crawler = $client->request('GET', '/creatrices');
        self::assertResponseIsSuccessful();
        $client->click($crawler->filter('a[href="/creatrices/'.$seller->getId().'"]')->link());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Atelier Zéphyr');
        self::assertSelectorTextContains('header', 'Couture en lin');
        self::assertSelectorTextContains('.product-card', 'Robe Camille');
    }

    public function testUnknownSellerIs404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/creatrices/999999999');

        self::assertResponseStatusCodeSame(404);
    }

    private function createSeller($user, string $companyName): Seller
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $seller = (new Seller())
            ->setCompanyName($companyName)
            ->setDescription('Couture en lin, pièces uniques.')
            ->setUser($em->find($user::class, $user->getId()));
        $em->persist($seller);
        $em->flush();
        // Removed before the user (cleanup runs in reverse order)
        $this->created[] = $seller;

        return $seller;
    }
}
