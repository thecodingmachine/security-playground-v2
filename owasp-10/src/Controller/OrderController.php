<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\SalesOrderRepository;
use App\Service\PaymentValidator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/orders', name: 'orders_index', methods: ['GET'])]
    public function index(Security $security, SalesOrderRepository $salesOrderRepository): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('orders/index.html.twig', [
            'orders' => $salesOrderRepository->findByOwner($currentUser),
        ]);
    }

    // ⚠️ Route volontairement vulnérable pour le challenge A10
    #[Route('/orders/{id}/confirm', name: 'orders_confirm', methods: ['POST'])]
    public function confirm(
        int $id,
        Request $request,
        Security $security,
        SalesOrderRepository $salesOrderRepository,
        EntityManagerInterface $entityManager,
        PaymentValidator $paymentValidator,
        LoggerInterface $logger
    ): JsonResponse {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $order = $salesOrderRepository->findOneForOwnerById($id, $currentUser);

        if ($order === null) {
            throw $this->createNotFoundException();
        }

        if ($order->getStatus() !== 'pending') {
            return new JsonResponse([
                'success' => false,
                'message' => 'La commande est déjà traitée.',
                'status' => $order->getStatus(),
            ], Response::HTTP_CONFLICT);
        }

        // ⚠️ VULNÉRABLE — Mishandling of Exceptional Conditions : état métier enregistré avant la validation critique.
        $order->markAsConfirmed();

        $invoice = (new Invoice())
            ->setSalesOrder($order)
            ->setIssuedTo($currentUser)
            ->setInvoiceNumber(sprintf('INV-%d-%s', time(), $order->getReference()))
            ->setTotalCents($order->getAmountCents())
            ->setCurrency($order->getCurrency());

        $entityManager->persist($invoice);
        $entityManager->flush();

        try {
            $paymentValidator->validatePayment($order, $request->request->getBoolean('simulate_failure'));
        } catch (\Throwable $exception) {
            $logger->warning('Payment validation failed, but order is already confirmed', [
                'order_reference' => $order->getReference(),
                'exception' => $exception,
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'status' => $order->getStatus(),
            'invoice_number' => $invoice->getInvoiceNumber(),
        ]);
    }

    #[Route('/orders/{id}/confirm/secure', name: 'orders_confirm_secure', methods: ['POST'])]
    public function confirmSecure(
        int $id,
        Request $request,
        Security $security,
        SalesOrderRepository $salesOrderRepository,
        EntityManagerInterface $entityManager,
        PaymentValidator $paymentValidator,
        LoggerInterface $logger
    ): JsonResponse {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $order = $salesOrderRepository->findOneForOwnerById($id, $currentUser);

        if ($order === null) {
            throw $this->createNotFoundException();
        }

        if ($order->getStatus() !== 'pending') {
            return new JsonResponse([
                'success' => false,
                'message' => 'La commande est déjà traitée.',
                'status' => $order->getStatus(),
            ], Response::HTTP_CONFLICT);
        }

        try {
            $entityManager->wrapInTransaction(
                function (EntityManagerInterface $transactionManager) use (
                    $order,
                    $request,
                    $paymentValidator,
                    $currentUser
                ): void {
                    $paymentValidator->validatePayment($order, $request->request->getBoolean('simulate_failure'));

                    $order->markAsConfirmed();

                    $invoice = (new Invoice())
                        ->setSalesOrder($order)
                        ->setIssuedTo($currentUser)
                        ->setInvoiceNumber(sprintf('INV-SAFE-%d-%s', time(), $order->getReference()))
                        ->setTotalCents($order->getAmountCents())
                        ->setCurrency($order->getCurrency());

                    $transactionManager->persist($invoice);
                }
            );
        } catch (\Throwable $exception) {
            $logger->error('Order confirmation failed', [
                'exception' => $exception,
                'order_reference' => $order->getReference(),
            ]);

            return new JsonResponse([
                'success' => false,
                'message' => "La commande n'a pas pu être confirmée.",
            ], Response::HTTP_CONFLICT);
        }

        return new JsonResponse([
            'success' => true,
            'status' => $order->getStatus(),
        ]);
    }
}
