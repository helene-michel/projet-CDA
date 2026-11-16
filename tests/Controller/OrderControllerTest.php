<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\StripePayment;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        // Keeps the Stripe stub between requests
        $this->client->disableReboot();
    }

    public function testCheckoutRequiresLogin(): void
    {
        $this->client->request('POST', '/order/checkout');

        self::assertResponseRedirects('/login');
    }

    public function testCheckoutCreatesPendingOrderAndRedirectsToStripe(): void
    {
        $stripe = $this->stubStripe(paid: false);
        $stripe->expects(self::once())->method('createCheckoutUrl')->willReturn('https://checkout.stripe.com/c/pay/test');
        $user = $this->loginWithCart(quantity: 2);

        $this->checkout();

        self::assertResponseRedirects('https://checkout.stripe.com/c/pay/test', 303);
        $order = $this->findOrder($user);
        self::assertSame(OrderStatus::Pending, $order->getStatus());
        self::assertCount(1, $order->getLines());
        $line = $order->getLines()->first();
        self::assertSame('Robe Camille', $line->getProductName());
        self::assertNotNull($line->getSeller());
        self::assertSame(static::getContainer()->get(\App\Repository\PlatformSettingRepository::class)->get()->getCommissionRate(), $line->getCommissionRate());
        // 2 x 107,40
        self::assertSame(214.8, $order->getTotal());
    }

    public function testSuccessMarksOrderPaidAndEmptiesCart(): void
    {
        $this->stubStripe(paid: true);
        $user = $this->loginWithCart(quantity: 1);
        $this->checkout();
        $order = $this->findOrder($user);

        $this->client->request('GET', '/order/'.$order->getId().'/success');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Merci pour votre commande');
        self::assertSame(OrderStatus::Paid, $this->findOrder($user)->getStatus());
        self::assertSelectorNotExists('.cart-btn__count');

        $this->client->request('GET', '/order');
        self::assertSelectorTextContains('tbody', 'Payée');
    }

    public function testUnpaidOrderStaysPending(): void
    {
        $this->stubStripe(paid: false);
        $user = $this->loginWithCart(quantity: 1);
        $this->checkout();
        $order = $this->findOrder($user);

        $this->client->request('GET', '/order/'.$order->getId().'/success');

        self::assertSelectorTextContains('h1', 'Paiement en cours de vérification');
        self::assertSame(OrderStatus::Pending, $this->findOrder($user)->getStatus());
    }

    public function testCannotSeeSomeoneElseOrder(): void
    {
        $this->stubStripe(paid: true);
        $owner = $this->loginWithCart(quantity: 1);
        $this->checkout();
        $order = $this->findOrder($owner);

        $this->client->loginUser($this->createUser('Intrus'));
        $this->client->request('GET', '/order/'.$order->getId().'/success');

        self::assertResponseStatusCodeSame(403);
    }

    private function stubStripe(bool $paid): StripePayment&\PHPUnit\Framework\MockObject\MockObject
    {
        $stripe = $this->createMock(StripePayment::class);
        $stripe->method('createCheckoutUrl')->willReturn('https://checkout.stripe.com/c/pay/test');
        $stripe->method('isPaid')->willReturn($paid);
        static::getContainer()->set(StripePayment::class, $stripe);

        return $stripe;
    }

    private function loginWithCart(int $quantity): User
    {
        $product = $this->createProduct('Robe Camille', 89.5, 20);
        $user = $this->createUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/article/'.$product->getId());
        $form = $crawler->filter('form[action="/cart/add/'.$product->getId().'"]')->form();
        $form['size']->select('m');
        $form['quantity'] = (string) $quantity;
        $this->client->submit($form);

        return $user;
    }

    private function checkout(): void
    {
        $crawler = $this->client->request('GET', '/cart');
        $this->client->submit($crawler->filter('form[action="/order/checkout"]')->form());
    }

    private function findOrder(User $user): Order
    {
        $repository = static::getContainer()->get(OrderRepository::class);
        $order = $repository->findOneBy(['user' => $user->getId()]);
        self::assertNotNull($order);
        // Fresh data from the DB, not the identity map
        static::getContainer()->get('doctrine')->getManager()->refresh($order);

        return $order;
    }
}
