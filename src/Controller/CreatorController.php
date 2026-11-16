<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Repository\ProductRepository;
use App\Repository\SellerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Public seller pages ("/seller" is the seller dashboard)
final class CreatorController extends AbstractController
{
    #[Route('/creatrices', name: 'app_creator_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('creator/index.html.twig', [
            'sellers' => $sellerRepository->findBy([], ['companyName' => 'ASC']),
        ]);
    }

    #[Route('/creatrices/{id}', name: 'app_creator_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Seller $seller, ProductRepository $productRepository): Response
    {
        return $this->render('creator/show.html.twig', [
            'seller' => $seller,
            'products' => $seller->getUser() ? $productRepository->findBy(['user' => $seller->getUser()], ['id' => 'DESC']) : [],
        ]);
    }
}
