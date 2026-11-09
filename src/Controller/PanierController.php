<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response

    {
        return $this->render('panier/list.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_panier_add', options:['expose'=>true])]
    public function add(
        Products $product,
        Request $request
    ): Response
    {
         $session = $request->getSession();
         $panier = $session->get('panier',[]);
         $panier[] = $product;
         $session->set('panier', $panier);
        return $this->redirectToRoute('app_panier');
    }
}
