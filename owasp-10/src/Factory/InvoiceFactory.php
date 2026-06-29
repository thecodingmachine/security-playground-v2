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
            'invoiceNumber' => sprintf('INV-2026-%04d', self::faker()->numberBetween(1000, 9999)),
            'totalCents' => self::faker()->numberBetween(1500, 12000),
            'currency' => 'EUR',
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
