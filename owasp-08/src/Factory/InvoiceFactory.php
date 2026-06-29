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
            'order' => OrderFactory::new(),
            'invoiceNumber' => sprintf('INV-2026-%05d', self::faker()->numberBetween(1, 99999)),
            'amountCents' => self::faker()->numberBetween(1000, 30000),
            'currency' => 'EUR',
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
