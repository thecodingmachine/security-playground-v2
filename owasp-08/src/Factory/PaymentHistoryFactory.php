<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PaymentHistory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PaymentHistory>
 */
final class PaymentHistoryFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PaymentHistory::class;
    }

    protected function defaults(): array
    {
        return [
            'order' => OrderFactory::new(),
            'eventId' => sprintf('evt_%s', self::faker()->uuid()),
            'eventType' => 'payment_succeeded',
            'payloadSnapshot' => '{}',
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
