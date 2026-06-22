<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NetworkController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Dashboard Traefik exposé
    #[Route('/network', name: 'network_index', methods: ['GET'])]
    public function index(): Response
    {
        // ⚠️ VULNÉRABLE — Security Misconfiguration : le dashboard et l'API Traefik sont publiquement accessibles.
        return $this->render('network/index.html.twig');
    }
}
