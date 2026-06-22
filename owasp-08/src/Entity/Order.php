<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 40, unique: true)]
    private string $reference = '';

    #[ORM\Column(length: 20)]
    private string $status = 'pending';

    #[ORM\Column]
    private int $amountCents = 0;

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $customer;

    /** @var Collection<int, Invoice> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Invoice::class)]
    private Collection $invoices;

    /** @var Collection<int, PaymentHistory> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: PaymentHistory::class)]
    private Collection $paymentHistories;

    /** @var Collection<int, InternalNotification> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: InternalNotification::class)]
    private Collection $internalNotifications;

    /** @var Collection<int, LoyaltyCredit> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: LoyaltyCredit::class)]
    private Collection $loyaltyCredits;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->paymentHistories = new ArrayCollection();
        $this->internalNotifications = new ArrayCollection();
        $this->loyaltyCredits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id > 0 ? $this->id : null;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAmountCents(): int
    {
        return $this->amountCents;
    }

    public function setAmountCents(int $amountCents): self
    {
        $this->amountCents = $amountCents;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCustomer(): User
    {
        return $this->customer;
    }

    public function setCustomer(User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /** @return Collection<int, Invoice> */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    /** @return Collection<int, PaymentHistory> */
    public function getPaymentHistories(): Collection
    {
        return $this->paymentHistories;
    }

    /** @return Collection<int, InternalNotification> */
    public function getInternalNotifications(): Collection
    {
        return $this->internalNotifications;
    }

    /** @return Collection<int, LoyaltyCredit> */
    public function getLoyaltyCredits(): Collection
    {
        return $this->loyaltyCredits;
    }
}
