<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Order;
use App\Repository\OrderRepository;

final class SafePaymentReturnService
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function handleReturn(string $reference, string $_status, int $_amountCents, string $_currency): ?Order
    {
        return $this->orderRepository->findOneByReference($reference);
    }
}
