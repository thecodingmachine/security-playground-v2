<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge XSS contextuelle
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function index(Request $request, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $filter = mb_substr($request->query->getString('filter'), 0, 120);

        // ⚠️ VULNÉRABLE — XSS contextuelle : la valeur utilisateur est injectée brute dans un script inline.
        return $this->render('dashboard/index.html.twig', [
            'current_user' => $currentUser,
            'filter' => $filter,
            'sales' => [
                ['label' => 'Pipeline en cours', 'value' => '248 400 EUR'],
                ['label' => 'Devis à valider', 'value' => '37'],
                ['label' => 'Tickets support ouverts', 'value' => '12'],
            ],
        ]);
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->redirectToRoute('dashboard');
    }
}
