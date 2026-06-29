<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerNoteRepository;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class InternalApiController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge CORS trop permissif
    #[Route('/api/internal/profile', name: 'api_internal_profile', methods: ['GET', 'OPTIONS'])]
    public function profile(
        Request $request,
        Security $security,
        CustomerNoteRepository $customerNoteRepository,
        InvoiceRepository $invoiceRepository
    ): JsonResponse {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $origin = $request->headers->get('Origin', '*');

        // ⚠️ VULNÉRABLE — Security Misconfiguration : origine reflétée sans allowlist stricte avec credentials activés.
        $response = new JsonResponse([
            'username' => $currentUser->getUsername(),
            'role' => implode(', ', $currentUser->getRoles()),
            'internal_notes' => array_map(
                static fn ($note) => [
                    'account_ref' => $note->getAccountRef(),
                    'note' => $note->getNote(),
                ],
                $customerNoteRepository->findLatestByUser($currentUser)
            ),
            'invoices_summary' => array_map(
                static fn ($invoice) => [
                    'reference' => $invoice->getReference(),
                    'status' => $invoice->getStatus(),
                    'amount_cents' => $invoice->getAmountCents(),
                ],
                $invoiceRepository->findByUser($currentUser)
            ),
        ]);

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With, X-Training-Any');

        if ($request->isMethod('OPTIONS')) {
            $response->setContent(null);
            $response->setStatusCode(JsonResponse::HTTP_NO_CONTENT);
        }

        return $response;
    }
}
