<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET'])]
    public function forgotPassword(): Response
    {
        return $this->render('reset/forgot_password.html.twig');
    }

    // ⚠️ Route volontairement vulnérable pour le challenge Token prévisible
    #[Route('/forgot-password', name: 'forgot_password_submit', methods: ['POST'])]
    public function forgotPasswordSubmit(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        PasswordResetTokenRepository $passwordResetTokenRepository
    ): Response {
        $username = trim($request->request->getString('username'));
        $user = $userRepository->findOneByUsername($username);

        if ($user === null) {
            $this->addFlash('error', 'Compte introuvable.');

            return $this->redirectToRoute('forgot_password');
        }

        // Token aléatoire pour éviter toute prédictibilité.
        $tokenValue = bin2hex(random_bytes(32));

        $resetToken = (new PasswordResetToken())
            ->setUser($user)
            ->setToken($tokenValue)
            ->setExpiresAt(new \DateTimeImmutable('+15 minutes'));

        $entityManager->persist($resetToken);
        $entityManager->flush();

        return $this->render('reset/forgot_password.html.twig', [
            'issued_token' => $tokenValue,
            'issued_user' => $user,
            'recent_tokens' => $passwordResetTokenRepository->findLatestByUser($user),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        PasswordResetTokenRepository $passwordResetTokenRepository
    ): Response {
        $resetToken = $passwordResetTokenRepository->findOneBy(['token' => $token]);

        if (!$resetToken instanceof PasswordResetToken) {
            throw $this->createNotFoundException('Lien invalide.');
        }

        if ($request->isMethod('POST')) {
            return $this->render('reset/reset_password.html.twig', [
                'token' => $resetToken,
                'session_user' => $resetToken->getUser(),
            ]);
        }

        return $this->render('reset/reset_password.html.twig', [
            'token' => $resetToken,
        ]);
    }
}
