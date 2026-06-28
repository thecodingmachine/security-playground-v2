<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SalesOrder;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SalesOrder>
 */
final class SalesOrderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return SalesOrder::class;
    }

    protected function defaults(): array
    {
        return [
            'reference' => sprintf('ORDER-2026-%04d', self::faker()->numberBetween(1000, 9999)),
            'status' => 'pending',
            'amountCents' => self::faker()->numberBetween(1500, 12000),
            'currency' => 'EUR',
            'createdAt' => new \DateTimeImmutable('-1 day'),
        ];
    }
}
