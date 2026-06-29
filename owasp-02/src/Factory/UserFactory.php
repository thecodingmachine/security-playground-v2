<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        return [
            'username' => self::faker()->unique()->userName(),
            'fullName' => self::faker()->name(),
            'password' => '$2y$13$w2XNE0z5lFm6Y9Bz8jlYQ.AhY4P7D8z2F2QwS9f4v3i2h1g0KjL9K',
            'roles' => ['ROLE_USER'],
        ];
    }
}
