<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge SQL Injection catalogue
    #[Route('/products', name: 'product_index', methods: ['GET'])]
    public function index(Request $request, Security $security, ProductRepository $productRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $query = mb_substr($request->query->getString('q'), 0, 120);

        // ⚠️ VULNÉRABLE — Injection SQL : concaténation directe d'un paramètre utilisateur dans la requête.
        $products = $productRepository->findPublicCatalogVulnerable($query);

        return $this->render('products/index.html.twig', [
            'query' => $query,
            'products' => $products,
        ]);
    }
}
