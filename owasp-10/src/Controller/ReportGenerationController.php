<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Repository\ReportRepository;
use App\Service\ReportFileExporter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportGenerationController extends AbstractController
{
    #[Route('/reports', name: 'reports_index', methods: ['GET'])]
    public function index(Security $security, ReportRepository $reportRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $reports = in_array('ROLE_ADMIN', $currentUser->getRoles(), true)
            ? $reportRepository->findBy([], ['id' => 'ASC'])
            : $reportRepository->findByOwner($currentUser);

        return $this->render('reports/index.html.twig', [
            'reports' => $reports,
        ]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge A10
    #[Route('/reports/{id}/export', name: 'reports_export', methods: ['GET'])]
    public function export(
        int $id,
        Request $request,
        Security $security,
        ReportRepository $reportRepository,
        ReportFileExporter $reportFileExporter
    ): Response {
        $report = $this->loadAccessibleReport($id, $security, $reportRepository);

        // ⚠️ VULNÉRABLE — Mishandling of Exceptional Conditions : les détails techniques de l'exception sont renvoyés au client.
        try {
            $fileContent = $reportFileExporter->generate($report, $request->query->getBoolean('simulate_failure'));
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'success' => false,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($fileContent, Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    #[Route('/reports/{id}/export/secure', name: 'reports_export_secure', methods: ['GET'])]
    public function exportSecure(
        int $id,
        Request $request,
        Security $security,
        ReportRepository $reportRepository,
        ReportFileExporter $reportFileExporter,
        LoggerInterface $logger
    ): Response {
        $report = $this->loadAccessibleReport($id, $security, $reportRepository);

        try {
            $fileContent = $reportFileExporter->generate($report, $request->query->getBoolean('simulate_failure'));
        } catch (\Throwable $exception) {
            $logger->error('Report generation failed', [
                'exception' => $exception,
                'report_id' => $report->getId(),
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Le rapport ne peut pas être généré pour le moment.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($fileContent, Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    private function loadAccessibleReport(int $id, Security $security, ReportRepository $reportRepository): Report
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $report = $reportRepository->find($id);

        if (!$report instanceof Report) {
            throw $this->createNotFoundException();
        }

        $isAdmin = in_array('ROLE_ADMIN', $currentUser->getRoles(), true);

        if (!$isAdmin && $report->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        return $report;
    }
}
