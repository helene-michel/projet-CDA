<?php

namespace App\Controller\Dashboard;

use App\Form\PlatformSettingType;
use App\Repository\OrderLineRepository;
use App\Repository\PlatformSettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/payment', name: 'admin_'), IsGranted('ROLE_ADMIN')]
final class PaymentControllerAdmin extends AbstractController
{
    #[Route(name: 'app_payment_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PlatformSettingRepository $settingRepository, OrderLineRepository $orderLineRepository, EntityManagerInterface $em): Response
    {
        $setting = $settingRepository->get();
        $form = $this->createForm(PlatformSettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Le taux de commission a été mis à jour.');

            return $this->redirectToRoute('admin_app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        $sales = $orderLineRepository->salesBySeller();

        return $this->render('admin/payment/index.html.twig', [
            'form' => $form,
            'sales' => $sales,
            'totals' => [
                'revenue' => array_sum(array_column($sales, 'revenue')),
                'commission' => array_sum(array_column($sales, 'commission')),
                'payout' => array_sum(array_column($sales, 'payout')),
            ],
        ]);
    }
}
