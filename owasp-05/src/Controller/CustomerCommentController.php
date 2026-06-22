<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerComment;
use App\Entity\User;
use App\Repository\CustomerCommentRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerCommentController extends AbstractController
{
    #[Route('/customers', name: 'customer_index', methods: ['GET'])]
    public function customers(Security $security, CustomerRepository $customerRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('customer_comments/customers.html.twig', [
            'customers' => $customerRepository->findBy([], ['company' => 'ASC']),
        ]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge XSS stockée
    #[Route('/customers/{id}/comments', name: 'customer_comments_index', methods: ['GET'])]
    public function index(Customer $customer, Security $security, CustomerCommentRepository $customerCommentRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // ⚠️ VULNÉRABLE — XSS stockée : la vue affiche le commentaire utilisateur brut avec le filtre raw.
        return $this->render('customer_comments/index.html.twig', [
            'customer' => $customer,
            'comments' => $customerCommentRepository->findByCustomer($customer),
        ]);
    }

    #[Route('/customers/{id}/comments', name: 'customer_comments_store', methods: ['POST'])]
    public function store(
        Customer $customer,
        Request $request,
        Security $security,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $content = mb_substr(trim($request->request->getString('content')), 0, 2000);

        if ($content === '') {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');

            return $this->redirectToRoute('customer_comments_index', ['id' => $customer->getId()]);
        }

        $comment = (new CustomerComment())
            ->setCustomer($customer)
            ->setAuthor($currentUser)
            ->setContent($content);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('customer_comments_index', ['id' => $customer->getId()]);
    }
}
