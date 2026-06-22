<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Order;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Order>
 */
final class OrderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Order::class;
    }

    protected function defaults(): array
    {
        return [
            'reference' => sprintf('ORDER-2026-%04d', self::faker()->numberBetween(1, 9999)),
            'status' => 'pending',
            'amountCents' => self::faker()->numberBetween(1000, 30000),
            'currency' => 'EUR',
            'customer' => UserFactory::new(),
        ];
    }
}
