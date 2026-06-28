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
            'title' => sprintf('Rapport %s', self::faker()->sentence(3)),
            'storagePath' => sprintf('/srv/reports/%s.csv', self::faker()->uuid()),
            'isSensitive' => false,
            'isBroken' => false,
            'createdAt' => new \DateTimeImmutable('-2 hours'),
        ];
    }
}
