<?php

namespace App\Controller\Dashboard;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/order', name: 'admin_'), IsGranted('ROLE_ADMIN')]
final class OrderControllerAdmin extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $status = OrderStatus::tryFrom($request->query->getString('status'));
        $pagination = $paginator->paginate(
            $orderRepository->adminQuery($status, $request->query->getString('search')),
            $request->query->getInt('page', 1),
        );

        return $this->render('admin/order/index.html.twig', [
            'orders' => $pagination,
            'statusCount' => $orderRepository->countByStatus(),
            'currentStatus' => $status,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
