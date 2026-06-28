<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SalesOrder;

final class PaymentValidator
{
    public function validatePayment(SalesOrder $order, bool $simulateFailure): void
    {
        if ($simulateFailure) {
            throw new \RuntimeException(sprintf('Payment gateway timeout for order %s', $order->getReference()));
        }
    }
}
