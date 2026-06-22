<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\InvoiceRepository;
use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index(Security $security, ReportRepository $reportRepository, InvoiceRepository $invoiceRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $currentUser,
            'reports' => $reportRepository->findLatestByUser($currentUser),
            'invoices' => $invoiceRepository->findByUser($currentUser),
        ]);
    }
}
