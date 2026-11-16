<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\Cart;
use App\Service\OrderCreator;
use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['GET'])]
    public function index(#[CurrentUser] User $user, OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findBy(['user' => $user], ['orderedAt' => 'DESC']),
        ]);
    }

    #[Route('/order/checkout', name: 'app_order_checkout', methods: ['POST'])]
    #[IsCsrfTokenValid('checkout')]
    public function checkout(#[CurrentUser] User $user, OrderCreator $orderCreator, StripePayment $stripePayment, EntityManagerInterface $em): Response
    {
        $order = $orderCreator->createFromCart($user);
        if (!$order) {
            return $this->redirectToRoute('app_cart');
        }

        $url = $stripePayment->createCheckoutUrl($order);
        $em->flush();

        return $this->redirect($url, Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/{id}/success', name: 'app_order_success', methods: ['GET'])]
    public function success(Order $order, StripePayment $stripePayment, Cart $cart, EntityManagerInterface $em): Response
    {
        $this->denyUnlessOwner($order);

        if (!$order->isPaid() && $stripePayment->isPaid($order)) {
            $order->setStatus(OrderStatus::Paid);
            $em->flush();
            $cart->clear();
        }

        return $this->render('order/success.html.twig', ['order' => $order]);
    }

    #[Route('/order/{id}/cancel', name: 'app_order_cancel', methods: ['GET'])]
    public function cancel(Order $order): Response
    {
        $this->denyUnlessOwner($order);
        $this->addFlash('warning', 'Le paiement a été annulé, votre panier est conservé.');

        return $this->redirectToRoute('app_cart');
    }

    private function denyUnlessOwner(Order $order): void
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }
}
