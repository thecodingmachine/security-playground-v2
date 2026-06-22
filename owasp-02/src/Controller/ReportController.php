<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReportController extends AbstractController
{
    #[Route('/reports', name: 'reports_index', methods: ['GET'])]
    public function index(Security $security, ReportRepository $reportRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('reports/index.html.twig', [
            'reports' => $reportRepository->findLatestByUser($currentUser),
        ]);
    }

    #[Route('/reports/open', name: 'reports_open', methods: ['POST'])]
    public function open(Request $request): RedirectResponse
    {
        $id = $request->request->getInt('report_id');

        return $this->redirectToRoute('reports_show', ['id' => $id]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge Debug/Profiler exposé
    #[Route('/reports/{id}', name: 'reports_show', methods: ['GET'])]
    public function show(int $id, ReportRepository $reportRepository): Response
    {
        // ⚠️ VULNÉRABLE — Security Misconfiguration : avec APP_ENV=dev et APP_DEBUG=1, cette erreur révèle stack trace, chemins, headers et requêtes.
        $report = $reportRepository->find($id);

        if ($report === null) {
            throw new \RuntimeException(sprintf('Export pipeline failure for report #%d.', $id));
        }

        return $this->render('reports/show.html.twig', [
            'report' => $report,
        ]);
    }
}
