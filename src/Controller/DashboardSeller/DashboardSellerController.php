<?php

namespace App\Controller\DashboardSeller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/seller', name: 'seller_')]
class DashboardSellerController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_seller_dashboard_seller')]
    public function index(ProductRepository $productRepository, #[CurrentUser] User $user): Response
    {
        return $this->render('seller/dashboard/index.html.twig', [
            'productCount' => $productRepository->count(['user' => $user]),
        ]);
    }
}
