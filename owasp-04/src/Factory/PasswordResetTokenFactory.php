<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\PasswordResetToken;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PasswordResetToken>
 */
final class PasswordResetTokenFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return PasswordResetToken::class;
    }

    protected function defaults(): array
    {
        $createdAt = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 day', 'now'));

        return [
            'token' => md5(self::faker()->userName().(string) time()),
            'createdAt' => $createdAt,
            'expiresAt' => $createdAt->modify('+7 days'),
            'user' => UserFactory::new(),
        ];
    }
}
