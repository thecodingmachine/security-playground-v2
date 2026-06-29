<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminUserController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge A10
    #[Route('/admin/users', name: 'admin_users', methods: ['GET'])]
    public function users(Security $security, UserRepository $userRepository, LoggerInterface $logger): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // ⚠️ VULNÉRABLE — Mishandling of Exceptional Conditions : une exception d'autorisation est catchée puis ignorée.
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } catch (\Throwable $exception) {
            $logger->warning('Admin access check failed, continuing anyway', [
                'username' => $currentUser->getUsername(),
                'exception' => $exception,
            ]);
        }

        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'ASC']),
            'mode' => 'vulnerable',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/accounts', name: 'admin_accounts', methods: ['GET'])]
    public function accounts(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'ASC']),
            'mode' => 'secure',
        ]);
    }
}
