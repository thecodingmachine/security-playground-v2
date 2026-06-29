<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\ProcessedWebhookEvent;
use App\Repository\ProcessedWebhookEventRepository;
use Doctrine\ORM\EntityManagerInterface;

final class IdempotentSettlementService
{
    public function __construct(
        private readonly ProcessedWebhookEventRepository $processedWebhookEventRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function consume(string $eventId, callable $businessAction): bool
    {
        if ($eventId === '') {
            return false;
        }

        $this->entityManager->beginTransaction();

        try {
            $alreadyProcessed = $this->processedWebhookEventRepository->findOneBy([
                'eventId' => $eventId,
            ]);

            if ($alreadyProcessed !== null) {
                $this->entityManager->rollback();

                return false;
            }

            $businessAction();

            $processedEvent = (new ProcessedWebhookEvent())
                ->setEventId($eventId)
                ->setProcessedAt(new \DateTimeImmutable());
            $this->entityManager->persist($processedEvent);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return true;
        } catch (\Throwable) {
            $this->entityManager->rollback();

            throw new \RuntimeException('Unable to process webhook event atomically.');
        }
    }
}
