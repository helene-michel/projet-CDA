<?php

namespace App\Controller;

use App\Repository\SellerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    #[Route('/', name: 'app_base')]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('base/index.html.twig', [
            'sellers' => $sellerRepository->findBy([], ['id' => 'DESC'], 4),
        ]);
    }
}
