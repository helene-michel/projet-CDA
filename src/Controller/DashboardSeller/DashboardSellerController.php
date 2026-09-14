<?php

namespace App\Controller\DashboardSeller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/seller', name:'seller_')]
class DashboardSellerController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_seller_dashboard_seller')]
    public function index(): Response
    {
        return $this->render('dashboard_seller/dashboard_seller/index.html.twig', [
            'controller_name' => 'DashboardSeller/DashboardSellerController',
        ]);
    }
}
