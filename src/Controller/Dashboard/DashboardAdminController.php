<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin')]
final class DashboardAdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard_admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard_admin/list.html.twig', [
            'controller_name' => 'DashboardAdminController',
        ]);
    }
}
