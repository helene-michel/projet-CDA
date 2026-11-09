<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    #[IsCsrfTokenValid('add_cart')]
    public function add(
        EntityManagerInterface $em, 
        Request $request,
        Products $products
        ): Response
    {

        $cart = [
            "produit"=>$products,
            "taille"=>$request->request->get("taille"),
            "couleur"=>$request->request->get("color"),
            "quantite"=>$request->request->get("number")
        ];
        $this->addFlash('success', 'Votre article à bien été ajouté au panier.');
        $session = $request->getSession();
        $cartSession = $session->get('cart') ?? [];
        array_push($cartSession,$cart);

        $session->set('cart', $cartSession);
        
        return $this->redirectToRoute('app_article', ['id'=>$products->getId()]);
    }
}
