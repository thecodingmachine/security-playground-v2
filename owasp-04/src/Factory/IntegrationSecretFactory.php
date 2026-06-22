<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\IntegrationSecret;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<IntegrationSecret>
 */
final class IntegrationSecretFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return IntegrationSecret::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->sentence(3),
            'encryptedValue' => base64_encode(self::faker()->sentence(8)),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 days', 'now')),
            'user' => UserFactory::new(),
        ];
    }
}
