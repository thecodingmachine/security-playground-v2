<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\CustomerComment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CustomerComment>
 */
final class CustomerCommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CustomerComment::class;
    }

    protected function defaults(): array
    {
        return [
            'customer' => CustomerFactory::new(),
            'author' => UserFactory::new(),
            'content' => self::faker()->paragraph(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 month')),
        ];
    }
}
