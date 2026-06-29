<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PaymentHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentHistoryRepository::class)]
class PaymentHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'paymentHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column(length: 80)]
    private string $eventId = '';

    #[ORM\Column(length: 80)]
    private string $eventType = '';

    #[ORM\Column(type: 'text')]
    private string $payloadSnapshot = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id > 0 ? $this->id : null;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
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

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getPayloadSnapshot(): string
    {
        return $this->payloadSnapshot;
    }

    public function setPayloadSnapshot(string $payloadSnapshot): self
    {
        $this->payloadSnapshot = $payloadSnapshot;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
