<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\SensitiveNote;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SensitiveNote>
 */
final class SensitiveNoteFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return SensitiveNote::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'encodedValue' => base64_encode(self::faker()->sentence(8)),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 days', 'now')),
            'user' => UserFactory::new(),
        ];
    }
}
