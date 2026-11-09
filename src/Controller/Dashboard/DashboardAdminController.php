<?php

namespace App\Controller\Dashboard;

use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_'), IsGranted('ROLE_ADMIN')]
final class DashboardAdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(UserRepository $userRepository, ProductRepository $productRepository): Response
    {
        return $this->render('admin/dashboard_admin/index.html.twig', [
            'userCount' => $userRepository->count(),
            'articleCount' => $productRepository->count(),
            'activeSellers' => $userRepository->countGroupedByRole(['ROLE_SELLER'])['ROLE_SELLER'],
            'latestUsers' => $userRepository->findBy([], ['id' => 'DESC'], 5),
        ]);
    }
}
