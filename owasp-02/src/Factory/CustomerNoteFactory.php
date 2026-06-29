<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\CustomerNote;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CustomerNote>
 */
final class CustomerNoteFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CustomerNote::class;
    }

    protected function defaults(): array
    {
        return [
            'accountRef' => sprintf('ACCT-%s', self::faker()->numerify('##-####')),
            'note' => self::faker()->sentence(12),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-15 days', 'now')),
            'user' => UserFactory::new(),
        ];
    }
}
