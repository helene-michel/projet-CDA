<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(Cart $cart): Response
    {
        $lines = $cart->getLines();

        return $this->render('cart/index.html.twig', [
            'lines' => $lines,
            'total' => array_sum(array_column($lines, 'total')),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    #[IsCsrfTokenValid('add_cart')]
    public function add(Product $product, Request $request, Cart $cart): Response
    {
        $cart->add(
            $product,
            $request->request->getString('size') ?: null,
            $request->request->getInt('color') ?: null,
            $request->request->getInt('quantity', 1),
        );
        $this->addFlash('success', 'Votre article a bien été ajouté au panier.');

        return $this->redirectToRoute('app_article', ['id' => $product->getId()]);
    }

    #[Route('/cart/remove/{index}', name: 'app_cart_remove', methods: ['POST'], requirements: ['index' => '\d+'])]
    #[IsCsrfTokenValid('remove_cart')]
    public function remove(int $index, Cart $cart): Response
    {
        $cart->remove($index);

        return $this->redirectToRoute('app_cart');
    }
}
