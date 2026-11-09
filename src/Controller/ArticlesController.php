<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tags;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ArticlesController extends AbstractController
{
    #[Route('/articles/{id}', name: 'app_articles')]
    public function index(
       Category $category,
       EntityManagerInterface $entityManager
    ): Response
    {
        $tags = $entityManager->getRepository(Tags::class)->findAll();
        $productMax = $entityManager->getRepository(Products::class)->priceMax($category);
        return $this->render('articles/list.html.twig', [
           'category'=>$category,
           'tags'=>$tags,
           'productMax'=>$productMax
        ]);
    }
    #[Route('/articles/{id}/load', name: 'app_articles_load', options:['expose'=>true])]
    public function getArticles(
        Category $category,
        EntityManagerInterface $entityManager,
        Request $request
    ){
   
         
        $products = $entityManager->getRepository(Products::class)->getByCategoryAndTags(
            $category->getId(),
            $request->query->all('tags'),
            // "59.99" ou "" : float ou null, sans troncature ni erreur de type
            $request->query->filter('prix', null, \FILTER_VALIDATE_FLOAT, ['flags' => \FILTER_NULL_ON_FAILURE]),
            $request->query->getString('tri'),
        );
                 
        return $this->json($products, 200);


    }

    #[Route('/article/{id}', name: 'app_article', options:['expose'=>true])]
    public function showArticle(
        Products $products,
        Request $request
    ): Response
    {
        $session = $request->getSession();
        dump($session->get('cart'));
        return $this->render('articles/show.html.twig', [
            'products' => $products
        ]);
    }
}
