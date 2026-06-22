<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\InternalNotification;
use App\Entity\Invoice;
use App\Entity\LoyaltyCredit;
use App\Entity\PaymentHistory;
use App\Repository\InvoiceRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentSettlementWebhookController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Software or Data Integrity Failure
    #[Route('/webhooks/payment/settlement', name: 'webhooks_payment_settlement', methods: ['POST'])]
    public function __invoke(
        Request $request,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        EntityManagerInterface $entityManager,
        #[Autowire('%env(string:WEBHOOK_SHARED_SECRET)%')] string $webhookSecret
    ): JsonResponse {
        $payload = $request->getContent();
        $receivedSignature = $request->headers->get('X-Signature');
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (!is_string($receivedSignature) || !hash_equals($expectedSignature, $receivedSignature)) {
            throw new AccessDeniedHttpException('Invalid webhook signature.');
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('Invalid JSON payload.');
        }

        $eventId = $data['event_id'] ?? null;
        $eventType = $data['event_type'] ?? null;
        $orderReference = $data['order_reference'] ?? null;

        if (!is_string($eventId) || !is_string($eventType) || !is_string($orderReference)) {
            throw new BadRequestHttpException('Missing webhook fields.');
        }

        $order = $orderRepository->findOneByReference($orderReference);

        if ($order === null) {
            throw $this->createNotFoundException('Order not found.');
        }

        if ($eventType !== 'payment_succeeded') {
            return new JsonResponse([
                'status' => 'ignored',
                'reason' => 'unsupported_event_type',
            ]);
        }

        // ⚠️ VULNÉRABLE — Software or Data Integrity Failure : l'event_id peut être rejoué sans contrôle d'idempotence.
        $invoice = (new Invoice())
            ->setOrder($order)
            ->setInvoiceNumber(sprintf('INV-%s-%03d', $order->getReference(), $invoiceRepository->nextSequenceForOrder($order)))
            ->setAmountCents($order->getAmountCents())
            ->setCurrency($order->getCurrency())
            ->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($invoice);

        $history = (new PaymentHistory())
            ->setOrder($order)
            ->setEventId($eventId)
            ->setEventType($eventType)
            ->setPayloadSnapshot($payload)
            ->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($history);

        $credit = (new LoyaltyCredit())
            ->setOrder($order)
            ->setBeneficiary($order->getCustomer())
            ->setPoints(50)
            ->setReason('Paiement confirmé par webhook')
            ->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($credit);

        $notification = (new InternalNotification())
            ->setOrder($order)
            ->setMessage(sprintf('Commande %s réglée, facture générée.', $order->getReference()))
            ->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($notification);

        $entityManager->flush();

        return new JsonResponse([
            'status' => 'processed',
            'event_id' => $eventId,
        ]);
    }
}
