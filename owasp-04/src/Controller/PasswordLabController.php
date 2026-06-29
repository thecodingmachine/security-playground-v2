<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PasswordLabController extends AbstractController
{
    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    // ⚠️ Route volontairement vulnérable pour le challenge MD5
    #[Route('/api/lab/register', name: 'api_lab_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $username = trim($request->request->getString('username'));
        $fullName = trim($request->request->getString('full_name'));
        $plainPassword = $request->request->getString('password');

        if ($username === '' || $fullName === '' || $plainPassword === '') {
            return $this->errorResponse($request, 'Tous les champs sont requis.');
        }

        if ($userRepository->findOneByUsername($username) !== null) {
            return $this->errorResponse($request, 'Ce nom utilisateur est déjà utilisé.');
        }

        // ⚠️ VULNÉRABLE — Cryptographic Failure : md5() est rapide, prévisible et inadapté pour stocker un mot de passe.
        $user = (new User())
            ->setUsername($username)
            ->setFullName($fullName)
            ->setRoles(['ROLE_USER'])
            ->setPassword(md5($plainPassword));

        $entityManager->persist($user);
        $entityManager->flush();

        if ($request->getPreferredFormat() === 'json') {
            return new JsonResponse(['status' => 'created'], JsonResponse::HTTP_CREATED);
        }

        $this->addFlash('success', 'Accès opérateur créé. Connectez-vous.');

        return $this->redirectToRoute('login');
    }

    #[Route('/lab/password-dump', name: 'lab_password_dump', methods: ['GET'])]
    public function passwordDump(UserRepository $userRepository): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $dictionaryPath = (is_string($projectDir) ? $projectDir : '').'/resources/training/weak-password-dictionary.txt';
        $dictionary = [];

        if (is_file($dictionaryPath)) {
            $lines = file($dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                $dictionary = array_map('trim', $lines);
            }
        }

        return $this->render('lab/password_dump.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'ASC']),
            'dictionary' => $dictionary,
        ]);
    }

    private function errorResponse(Request $request, string $message): Response
    {
        if ($request->getPreferredFormat() === 'json') {
            return new JsonResponse(['error' => $message], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->addFlash('error', $message);

        return new RedirectResponse($this->generateUrl('register_form'));
    }
}
