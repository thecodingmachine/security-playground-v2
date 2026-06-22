<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Report;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Report>
 */
final class ReportFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Report::class;
    }

    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(4),
            'content' => self::faker()->paragraph(3),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-30 days', 'now')),
            'user' => UserFactory::new(),
        ];
    }
}
