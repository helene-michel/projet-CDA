<?php

namespace App\Service;

use App\Entity\Order;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripePayment
{
    public function __construct(
        #[Autowire('%env(STRIPE_SECRET_KEY)%')] private string $secretKey,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    // Returns the Stripe-hosted payment page URL
    public function createCheckoutUrl(Order $order): string
    {
        $lineItems = [];
        foreach ($order->getLines() as $line) {
            $lineItems[] = [
                'quantity' => $line->getQuantity(),
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($line->getUnitPrice() * 100),
                    'product_data' => ['name' => $line->getProductName()],
                ],
            ];
        }

        $session = $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'customer_email' => $order->getUser()->getEmail(),
            'client_reference_id' => (string) $order->getId(),
            'success_url' => $this->urlGenerator->generate('app_order_success', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_order_cancel', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $order->setStripeSessionId($session->id);

        return $session->url;
    }

    public function isPaid(Order $order): bool
    {
        if (!$order->getStripeSessionId()) {
            return false;
        }

        return 'paid' === $this->client()->checkout->sessions->retrieve($order->getStripeSessionId())->payment_status;
    }

    private function client(): StripeClient
    {
        return new StripeClient($this->secretKey);
    }
}
