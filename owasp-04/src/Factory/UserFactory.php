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
            'password' => md5('password'),
            'roles' => ['ROLE_USER'],
        ];
    }
}
