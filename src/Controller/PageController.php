<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Model\ContactMessage;
use App\Service\ContactMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

// Static pages
final class PageController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_legal', methods: ['GET'])]
    public function legal(): Response
    {
        return $this->render('page/legal.html.twig');
    }

    #[Route('/livraison', name: 'app_delivery', methods: ['GET'])]
    public function delivery(): Response
    {
        return $this->render('page/delivery.html.twig');
    }

    #[Route('/retours', name: 'app_returns', methods: ['GET'])]
    public function returns(): Response
    {
        return $this->render('page/returns.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, ContactMailer $contactMailer, #[CurrentUser] ?User $user): Response
    {
        $contact = new ContactMessage();
        if ($user) {
            $contact->name = $user->getDisplayName();
            $contact->email = $user->getEmail();
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactMailer->send($contact);
            $this->addFlash('success', 'Votre message a bien été envoyé, nous vous répondrons rapidement.');

            return $this->redirectToRoute('app_contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
