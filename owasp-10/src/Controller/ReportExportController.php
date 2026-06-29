<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReportRepository;
use App\Security\PermissionChecker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportExportController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge A10
    #[Route('/finance/export', name: 'finance_export', methods: ['GET'])]
    public function export(
        Request $request,
        Security $security,
        PermissionChecker $permissionChecker,
        ReportRepository $reportRepository,
        LoggerInterface $logger
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$request->query->getBoolean('download')) {
            return $this->render('reports/export.html.twig', [
                'route_name' => 'finance_export',
            ]);
        }

        $simulateFailure = $request->query->getBoolean('simulate_failure');

        // ⚠️ VULNÉRABLE — Mishandling of Exceptional Conditions : fallback permissif si le service de permission échoue.
        try {
            $canExport = $permissionChecker->canExportSensitiveData($currentUser, $simulateFailure);
        } catch (\Throwable $exception) {
            $logger->warning('Permission check failed, allowing export by fallback', [
                'exception' => $exception,
                'username' => $currentUser->getUsername(),
            ]);
            $canExport = true;
        }

        if (!$canExport) {
            throw $this->createAccessDeniedException();
        }

        return $this->buildCsvResponse($reportRepository);
    }

    #[Route('/finance/export/secure', name: 'finance_export_secure', methods: ['GET'])]
    public function exportSecure(
        Request $request,
        Security $security,
        PermissionChecker $permissionChecker,
        ReportRepository $reportRepository,
        LoggerInterface $logger
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$request->query->getBoolean('download')) {
            return $this->render('reports/export.html.twig', [
                'route_name' => 'finance_export_secure',
            ]);
        }

        try {
            $canExport = $permissionChecker->canExportSensitiveData(
                $currentUser,
                $request->query->getBoolean('simulate_failure')
            );
        } catch (\Throwable $exception) {
            $logger->error('Permission check failed', [
                'exception' => $exception,
                'username' => $currentUser->getUsername(),
            ]);

            throw $this->createAccessDeniedException();
        }

        if (!$canExport) {
            throw $this->createAccessDeniedException();
        }

        return $this->buildCsvResponse($reportRepository);
    }

    private function buildCsvResponse(ReportRepository $reportRepository): Response
    {
        $rows = $reportRepository->findSensitiveExportRows();
        $csv = "title;owner;created_at\n";

        foreach ($rows as $row) {
            $dateValue = $row['created_at'];
            $formattedDate = $dateValue instanceof \DateTimeInterface
                ? $dateValue->format('Y-m-d H:i:s')
                : (string) $dateValue;

            $csv .= sprintf("%s;%s;%s\n", $row['title'], $row['owner'], $formattedDate);
        }

        return new Response($csv, Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
