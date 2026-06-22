<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentReturnController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Software or Data Integrity Failure
    #[Route('/payment/return', name: 'payment_return', methods: ['GET'])]
    public function __invoke(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $orderReference = $request->query->getString('order_reference');
        $status = $request->query->getString('status');
        $amountCents = $request->query->getInt('amount_cents');
        $currency = $request->query->getString('currency');

        $order = $orderRepository->findOneByReference($orderReference);

        if ($order === null) {
            throw $this->createNotFoundException('Order not found.');
        }

        // ⚠️ VULNÉRABLE — Software or Data Integrity Failure : le statut venant du navigateur déclenche une décision critique.
        if ($status === 'paid' && $amountCents > 0 && $currency !== '') {
            $order->setStatus('paid');
            $entityManager->flush();
        }

        return $this->render('payment/return.html.twig', [
            'order' => $order,
            'status' => $status,
            'amount_cents' => $amountCents,
            'currency' => $currency,
        ]);
    }
}
