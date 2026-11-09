<?php

namespace App\Controller\DashboardSeller;

use App\Entity\Products;
use App\Entity\User;
use App\Security\Voter\ProductVoter;
use App\Form\ProductsType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use App\Repository\ProductsRepository;
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
    public function index(ProductsRepository $productsRepository, #[CurrentUser] User $user): Response
    {
        // Uniquement les produits du vendeur connecté (et non tout le catalogue).
        return $this->render('seller/products/index.html.twig', [
            'products' => $productsRepository->findBy(['user' => $user], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        #[CurrentUser] User $user,
    ): Response {
        $product = (new Products())->setUser($user)->setTVA(20.0);
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->uploadImages($form, $product, $fileUploader)) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été créé.', $product->getNom()));

            return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        // Formulaire invalide : render() renvoie automatiquement un statut 422 (requis par Turbo).
        return $this->render('seller/products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function show(Products $product): Response
    {
        return $this->render('seller/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function edit(
        Request $request,
        Products $product,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
    ): Response {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->uploadImages($form, $product, $fileUploader)) {
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été modifié.', $product->getNom()));

            return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seller/products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    #[IsGranted(ProductVoter::MANAGE, 'product')]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Le produit « %s » a été supprimé.', $product->getNom()));
        } else {
            $this->addFlash('error', 'La suppression a échoué, veuillez réessayer.');
        }

        return $this->redirectToRoute('seller_app_products_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Enregistre les photos envoyées et les ajoute à celles du produit.
     * Retourne false (avec une erreur sur le champ) si un fichier n'a pas pu être enregistré.
     */
    private function uploadImages(FormInterface $form, Products $product, FileUploader $fileUploader): bool
    {
        $filenames = $product->getImage() ?? [];
        try {
            foreach ($form->get('image')->getData() ?? [] as $imageFile) {
                $filenames[] = $fileUploader->upload($imageFile);
            }
        } catch (FileException) {
            $form->get('image')->addError(new FormError('Une photo n\'a pas pu être enregistrée, veuillez réessayer.'));

            return false;
        }
        $product->setImage($filenames);

        return true;
    }
}
