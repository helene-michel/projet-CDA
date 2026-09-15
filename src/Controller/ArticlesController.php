<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tags;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticlesController extends AbstractController
{
    #[Route('/articles/{id}', name: 'app_articles')]
    public function index(
        Category $category,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $tags = $entityManager->getRepository(Tags::class)->findAll();
        return $this->render('articles/list.html.twig', [
            'category' => $category,
            'tags' => $tags,
        ]);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function showArticle(
        Products $products,

    ): Response
    {
        return $this->render('articles/show.html.twig', [
            'products' => $products,
        ]);
    }
}
