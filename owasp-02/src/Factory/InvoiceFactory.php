<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Invoice;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Invoice>
 */
final class InvoiceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Invoice::class;
    }

    protected function defaults(): array
    {
        return [
            'reference' => sprintf('INV-%s', self::faker()->unique()->numerify('2026-###')),
            'amountCents' => self::faker()->numberBetween(5000, 90000),
            'status' => self::faker()->randomElement(['paid', 'pending', 'late']),
            'issuedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-45 days', 'now')),
            'user' => UserFactory::new(),
        ];
    }
}
