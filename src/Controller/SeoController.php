<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeoController extends AbstractController
{
    #[Route('/robots.txt', name: 'app_robots', methods: ['GET'], format: 'txt')]
    public function robots(): Response
    {
        $response = $this->render('seo/robots.txt.twig');
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');

        return $response->setPublic()->setMaxAge(86400);
    }

    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'], format: 'xml')]
    public function sitemap(CategoryRepository $categories, ProductsRepository $products): Response
    {
        $response = $this->render('seo/sitemap.xml.twig', [
            'categoryIds' => $categories->findAllIds(),
            'productIds' => $products->findAllIds(),
        ]);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');

        return $response->setPublic()->setMaxAge(3600);
    }
}
