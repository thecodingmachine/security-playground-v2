<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\InvoiceRepository;
use App\Repository\ReportRepository;
use App\Repository\SalesOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(
        Security $security,
        SalesOrderRepository $salesOrderRepository,
        InvoiceRepository $invoiceRepository,
        ReportRepository $reportRepository
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/index.html.twig', [
            'current_user' => $currentUser,
            'order_count' => count($salesOrderRepository->findByOwner($currentUser)),
            'invoice_count' => count($invoiceRepository->findBy(['issuedTo' => $currentUser])),
            'report_count' => count($reportRepository->findByOwner($currentUser)),
        ]);
    }
}
