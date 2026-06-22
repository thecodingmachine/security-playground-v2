<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class SignedWebhookPaymentService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%env(string:WEBHOOK_SHARED_SECRET)%')] private readonly string $webhookSecret
    ) {
    }

    public function process(string $payload, ?string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);

        if (!is_string($signature) || !hash_equals($expectedSignature, $signature)) {
            return false;
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return false;
        }

        $eventType = $data['event_type'] ?? null;
        $orderReference = $data['order_reference'] ?? null;
        $amountCents = $data['amount_cents'] ?? null;
        $currency = $data['currency'] ?? null;

        if (!is_string($eventType) || !is_string($orderReference) || !is_numeric($amountCents) || !is_string($currency)) {
            return false;
        }

        if ($eventType !== 'payment_succeeded') {
            return false;
        }

        $order = $this->orderRepository->findOneByReference($orderReference);

        if ($order === null) {
            return false;
        }

        if ($order->getAmountCents() !== (int) $amountCents || $order->getCurrency() !== $currency) {
            return false;
        }

        $order->setStatus('paid');
        $this->entityManager->flush();

        return true;
    }
}
