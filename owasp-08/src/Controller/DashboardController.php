<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\CatalogImportRepository;
use App\Repository\CatalogProductRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function index(
        Security $security,
        OrderRepository $orderRepository,
        CatalogProductRepository $catalogProductRepository,
        CatalogImportRepository $catalogImportRepository
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $orders = $orderRepository->findBy([], [
            'id' => 'DESC',
        ]);

        $pendingOrders = 0;
        $paidOrders = 0;

        foreach ($orders as $order) {
            if ($order->getStatus() === 'paid') {
                ++$paidOrders;
                continue;
            }

            ++$pendingOrders;
        }

        return $this->render('dashboard/index.html.twig', [
            'current_user' => $currentUser,
            'orders' => $orders,
            'pending_orders' => $pendingOrders,
            'paid_orders' => $paidOrders,
            'catalog_products_count' => $catalogProductRepository->count([]),
            'catalog_imports_count' => $catalogImportRepository->count([]),
        ]);
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->redirectToRoute('dashboard');
    }
}
