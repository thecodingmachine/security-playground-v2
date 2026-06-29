<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\LoyaltyCredit;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<LoyaltyCredit>
 */
final class LoyaltyCreditFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return LoyaltyCredit::class;
    }

    protected function defaults(): array
    {
        return [
            'beneficiary' => UserFactory::new(),
            'order' => OrderFactory::new(),
            'points' => 50,
            'reason' => 'Crédit fidélité',
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
