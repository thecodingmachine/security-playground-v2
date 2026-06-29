<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProcessedWebhookEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcessedWebhookEventRepository::class)]
class ProcessedWebhookEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 80, unique: true)]
    private string $eventId = '';

    #[ORM\Column]
    private \DateTimeImmutable $processedAt;

    public function getId(): ?int
    {
        return $this->id > 0 ? $this->id : null;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function setEventId(string $eventId): self
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getProcessedAt(): \DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(\DateTimeImmutable $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }
}
