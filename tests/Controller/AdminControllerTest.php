<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\OrderStatus;
use App\Entity\PlatformSetting;
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
            '/admin/category', '/admin/category/new', '/admin/tags', '/admin/tags/new', '/admin/color', '/admin/order', '/admin/payment'];

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

    public function testOrderListAndDetail(): void
    {
        $product = $this->createProduct('Robe Camille');
        $customer = $this->createUser('Zéphyrine');
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $order = (new Order())
            ->setUser($em->find(User::class, $customer->getId()))
            ->setStatus(OrderStatus::Paid)
            ->addLine((new OrderLine())
                ->setProduct($em->find($product::class, $product->getId()))
                ->setProductName('Robe Camille')
                ->setSize('m')
                ->setQuantity(2)
                ->setUnitPrice(107.4));
        $em->persist($order);
        $em->flush();

        $this->client->request('GET', '/admin/order', ['status' => 'paid', 'search' => 'Zéphyrine']);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.results-count', '1');
        self::assertSelectorTextContains('.users-table', $customer->getEmail());
        self::assertSelectorTextContains('.users-table', 'Payée');

        $this->client->request('GET', '/admin/order', ['status' => 'pending', 'search' => 'Zéphyrine']);
        self::assertSelectorTextContains('.results-count', '0');

        $this->client->request('GET', '/admin/order/'.$order->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Commande n° '.$order->getId());
        self::assertSelectorTextContains('.users-table', 'Robe Camille');
        self::assertSelectorTextContains('.users-table', "214,80\u{a0}€");
    }

    public function testOrderPageIsForbiddenToCustomers(): void
    {
        $this->client->loginUser($this->createUser('Cliente'));
        $this->client->request('GET', '/admin/order');

        self::assertResponseStatusCodeSame(403);
    }

    public function testPaymentPageShowsCommissionPerSeller(): void
    {
        $seller = $this->createUser('Zéphyrine', ['ROLE_SELLER']);
        $product = $this->createProduct('Robe Camille', owner: $seller);
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $order = (new Order())
            ->setUser($em->find(User::class, $this->createUser('Cliente')->getId()))
            ->setStatus(OrderStatus::Paid)
            ->addLine((new OrderLine())
                ->setProduct($em->find($product::class, $product->getId()))
                ->setSeller($em->find(User::class, $seller->getId()))
                ->setProductName('Robe Camille')
                ->setQuantity(2)
                ->setUnitPrice(100)
                ->setCommissionRate(10));
        $em->persist($order);
        $em->flush();

        $crawler = $this->client->request('GET', '/admin/payment');

        self::assertResponseIsSuccessful();
        $row = $crawler->filter('.users-table tbody tr')->reduce(fn ($tr) => str_contains($tr->text(), $seller->getEmail()));
        self::assertCount(1, $row);
        // 200 € of sales, 10 % commission
        self::assertStringContainsString("200,00\u{a0}€", $row->text());
        self::assertStringContainsString("20,00\u{a0}€", $row->text());
        self::assertStringContainsString("180,00\u{a0}€", $row->text());
    }

    public function testUpdateCommissionRate(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $previous = $em->getRepository(PlatformSetting::class)->get()->getCommissionRate();

        $crawler = $this->client->request('GET', '/admin/payment');
        $this->client->submit($crawler->filter('form[name="platform_setting"]')->form(['platform_setting[commissionRate]' => '150']));
        self::assertResponseStatusCodeSame(422);

        $this->client->submit($crawler->filter('form[name="platform_setting"]')->form(['platform_setting[commissionRate]' => '12.5']));
        self::assertResponseRedirects('/admin/payment');

        $em->clear();
        $setting = $em->getRepository(PlatformSetting::class)->get();
        self::assertSame(12.5, $setting->getCommissionRate());
        $setting->setCommissionRate($previous);
        $em->flush();
    }
}
