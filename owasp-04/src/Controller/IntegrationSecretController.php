<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\IntegrationSecret;
use App\Entity\User;
use App\Repository\IntegrationSecretRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IntegrationSecretController extends AbstractController
{
    #[Route('/integration-secrets', name: 'integration_secret_index', methods: ['GET'])]
    public function index(Security $security, IntegrationSecretRepository $integrationSecretRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('integration_secrets/index.html.twig', [
            'secrets' => $integrationSecretRepository->findByUser($currentUser),
        ]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge clé hardcodée
    #[Route('/integration-secrets', name: 'integration_secret_store', methods: ['POST'])]
    public function store(Request $request, Security $security, EntityManagerInterface $entityManager): RedirectResponse
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $name = trim($request->request->getString('name'));
        $value = trim($request->request->getString('value'));

        if ($name === '' || $value === '') {
            $this->addFlash('error', 'Le nom du connecteur et le secret sont requis.');

            return $this->redirectToRoute('integration_secret_index');
        }

        // ⚠️ VULNÉRABLE — Cryptographic Failure : clé par défaut hardcodée et partagée entre tous les environnements.
        $secretKey = 'default_key';
        $iv = substr(hash('sha256', $secretKey, true), 0, 16);
        $encrypted = openssl_encrypt($value, 'aes-256-cbc', $secretKey, 0, $iv);

        if (!is_string($encrypted)) {
            $this->addFlash('error', 'Chiffrement impossible.');

            return $this->redirectToRoute('integration_secret_index');
        }

        $secret = (new IntegrationSecret())
            ->setUser($currentUser)
            ->setName($name)
            ->setEncryptedValue($encrypted);

        $entityManager->persist($secret);
        $entityManager->flush();

        return $this->redirectToRoute('integration_secret_show', ['id' => $secret->getId()]);
    }

    #[Route('/integration-secrets/{id}', name: 'integration_secret_show', methods: ['GET'])]
    public function show(IntegrationSecret $secret, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $secret->getUser() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('integration_secrets/show.html.twig', [
            'secret' => $secret,
        ]);
    }

    #[Route('/integration-secrets/{id}/decrypt', name: 'integration_secret_decrypt', methods: ['POST'])]
    public function decrypt(IntegrationSecret $secret, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $secret->getUser() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        $secretKey = 'default_key';
        $iv = substr(hash('sha256', $secretKey, true), 0, 16);
        $decrypted = openssl_decrypt($secret->getEncryptedValue(), 'aes-256-cbc', $secretKey, 0, $iv);

        return $this->render('integration_secrets/show.html.twig', [
            'secret' => $secret,
            'decrypted_value' => $decrypted === false ? 'Déchiffrement impossible.' : $decrypted,
        ]);
    }
}
