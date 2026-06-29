<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\IntegrationSecretRepository;
use App\Repository\SensitiveNoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index(
        Security $security,
        SensitiveNoteRepository $sensitiveNoteRepository,
        IntegrationSecretRepository $integrationSecretRepository
    ): Response {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $notes = $sensitiveNoteRepository->findLatestByUser($currentUser);
        $secrets = $integrationSecretRepository->findByUser($currentUser);

        return $this->render('dashboard/index.html.twig', [
            'user' => $currentUser,
            'notes_count' => count($notes),
            'secrets_count' => count($secrets),
        ]);
    }
}
