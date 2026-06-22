<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DiagnosticsController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Fichier sensible exposé
    #[Route('/diagnostics', name: 'diagnostics_index', methods: ['GET'])]
    public function index(): Response
    {
        // ⚠️ VULNÉRABLE — Security Misconfiguration : la racine web du conteneur pointe vers le mauvais dossier et expose des fichiers sensibles.
        return $this->render('diagnostics/index.html.twig');
    }
}
