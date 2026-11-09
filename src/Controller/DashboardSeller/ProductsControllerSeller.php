<?php

namespace App\Controller\DashboardSeller;

use App\Entity\Product;
use App\Entity\User;
use App\Security\Voter\ProductVoter;
use App\Form\ProductType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Service\FileUploader;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/seller/products', name:'seller_')]
final class ProductsControllerSeller extends AbstractController
{
    #[Route(name: 'app_products_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, #[CurrentUser] User $user): Response
    {
        return $this->render('seller/products/index.html.twig', [
            'products' => $productRepository->findBy(['user' => $user], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        #[CurrentUser] User $user,
    ): Response {
        $product = (new Product())->setUser($user)->setVatRate(20.0);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->uploadImages($form, $product, $fileUploader)) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été créé.', $product->getName()));

            return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function show(Product $product): Response
    {
        return $this->render('seller/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function edit(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->uploadImages($form, $product, $fileUploader)) {
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été modifié.', $product->getName()));

            return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été supprimé.', $product->getName()));
        } else {
            $this->addFlash('error', 'La suppression a échoué, veuillez réessayer.');
        }

        return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadImages(FormInterface $form, Product $product, FileUploader $fileUploader): bool
    {
        try {
            $fileUploader->addProductImages($product, $form->get('images')->getData() ?? []);
        } catch (FileException) {
            $form->get('images')->addError(new FormError('Une photo n\'a pas pu être enregistrée, veuillez réessayer.'));

            return false;
        }

        return true;
    }
}
