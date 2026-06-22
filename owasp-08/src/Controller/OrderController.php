<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/orders', name: 'orders_index', methods: ['GET'])]
    public function index(Security $security, OrderRepository $orderRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $orders = $orderRepository->findBy([
            'customer' => $currentUser,
        ], [
            'id' => 'DESC',
        ]);

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}', name: 'orders_show', methods: ['GET'])]
    public function show(Order $order, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $order->getCustomer() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('orders/show.html.twig', [
            'order' => $order,
        ]);
    }
}
