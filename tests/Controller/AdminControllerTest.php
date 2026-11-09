<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->loginUser($this->createUser('Admin', ['ROLE_ADMIN']));
    }

    public function testAllAdminPagesRender(): void
    {
        $product = $this->createProduct();
        $pages = ['/admin/dashboard', '/admin/user', '/admin/products', '/admin/products/new',
            '/admin/products/'.$product->getId(), '/admin/products/'.$product->getId().'/edit',
            '/admin/category', '/admin/category/new', '/admin/tags', '/admin/tags/new', '/admin/color'];

        foreach ($pages as $page) {
            $this->client->request('GET', $page);
            self::assertResponseIsSuccessful($page);
        }
    }

    public function testDashboardShowsLatestUsers(): void
    {
        $user = $this->createUser('Camille');

        $this->client->request('GET', '/admin/dashboard');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.recent-list', $user->getEmail());
    }

    public function testUserPageDoesNotShowPasswordHash(): void
    {
        $user = $this->createUser('Camille');

        $this->client->request('GET', '/admin/user/'.$user->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Camille Lefèvre');
        self::assertSelectorTextNotContains('body', 'Password');
    }

    public function testEditUserKeepsPassword(): void
    {
        $user = $this->createUser('Camille');

        $crawler = $this->client->request('GET', '/admin/user/'.$user->getId().'/edit');
        $form = $crawler->filter('form[name="admin_user"]')->form();
        $form['admin_user[roles]'][1]->tick();
        $this->client->submit($form);

        self::assertResponseRedirects('/admin/user');
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $updated = $em->find(User::class, $user->getId());
        self::assertContains('ROLE_SELLER', $updated->getRoles());
        self::assertSame('x', $updated->getPassword());
    }

    public function testUserSearch(): void
    {
        $user = $this->createUser('Zéphyrine');

        $this->client->request('GET', '/admin/user', ['search' => 'Zéphyrine']);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.users-table', $user->getEmail());
        self::assertSelectorTextContains('.results-count', '1');
    }
}
