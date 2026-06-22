<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InfrastructureController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge MySQL/phpMyAdmin exposés
    #[Route('/infrastructure', name: 'infrastructure_index', methods: ['GET'])]
    public function index(): Response
    {
        // ⚠️ VULNÉRABLE — Security Misconfiguration : des services internes d'administration sont exposés hors du réseau privé Docker.
        return $this->render('infrastructure/index.html.twig');
    }
}
