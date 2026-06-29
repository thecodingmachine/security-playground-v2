<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LoyaltyCreditRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoyaltyCreditRepository::class)]
class LoyaltyCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'loyaltyCredits')]
    #[ORM\JoinColumn(nullable: false)]
    private User $beneficiary;

    #[ORM\ManyToOne(inversedBy: 'loyaltyCredits')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Column]
    private int $points = 0;

    #[ORM\Column(length: 140)]
    private string $reason = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function getId(): ?int
    {
        return $this->id > 0 ? $this->id : null;
    }

    public function getBeneficiary(): User
    {
        return $this->beneficiary;
    }

    public function setBeneficiary(User $beneficiary): self
    {
        $this->beneficiary = $beneficiary;

        return $this;
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

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

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
