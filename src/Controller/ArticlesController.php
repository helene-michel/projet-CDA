<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticlesController extends AbstractController
{
    #[Route('/articles/{id}', name: 'app_articles')]
    public function index(Category $category, TagRepository $tagRepository, ProductRepository $productRepository): Response
    {
        return $this->render('articles/list.html.twig', [
            'category' => $category,
            'tags' => $tagRepository->findBy([], ['name' => 'ASC']),
            'maxPrice' => $productRepository->priceMax($category),
        ]);
    }

    #[Route('/articles/{id}/load', name: 'app_articles_load', options: ['expose' => true])]
    public function load(Category $category, ProductRepository $productRepository, Request $request): JsonResponse
    {
        $products = $productRepository->getByCategoryAndTags(
            $category->getId(),
            $request->query->all('tags'),
            // "" -> null
            $request->query->filter('price', null, \FILTER_VALIDATE_FLOAT, ['flags' => \FILTER_NULL_ON_FAILURE]),
            $request->query->getString('sort'),
        );

        return $this->json($products);
    }

    #[Route('/article/{id}', name: 'app_article', options: ['expose' => true])]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        return $this->render('articles/show.html.twig', [
            'product' => $product,
            'similarProducts' => $productRepository->findSimilar($product),
        ]);
    }
}
