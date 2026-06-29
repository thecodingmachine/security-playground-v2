<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentWebhookController extends AbstractController
{
    // ⚠️ Route volontairement vulnérable pour le challenge Software or Data Integrity Failure
    #[Route('/webhooks/payment', name: 'webhooks_payment', methods: ['POST'])]
    public function __invoke(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            /** @var array<string, mixed> $payload */
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('Invalid JSON payload.');
        }

        $eventType = $payload['event_type'] ?? null;
        $orderReference = $payload['order_reference'] ?? null;

        if (!is_string($eventType) || !is_string($orderReference)) {
            throw new BadRequestHttpException('Missing webhook fields.');
        }

        $order = $orderRepository->findOneByReference($orderReference);

        if ($order === null) {
            throw $this->createNotFoundException('Order not found.');
        }

        // ⚠️ VULNÉRABLE — Software or Data Integrity Failure : le serveur croit le webhook sans signature.
        if ($eventType === 'payment_succeeded') {
            $order->setStatus('paid');
            $entityManager->flush();
        }

        return new JsonResponse([
            'status' => 'processed',
            'order_reference' => $orderReference,
        ]);
    }
}
