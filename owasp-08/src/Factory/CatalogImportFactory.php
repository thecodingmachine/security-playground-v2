<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\CatalogImport;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CatalogImport>
 */
final class CatalogImportFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CatalogImport::class;
    }

    protected function defaults(): array
    {
        return [
            'importedBy' => UserFactory::new(),
            'filename' => 'catalog.json',
            'checksum' => hash('sha256', self::faker()->uuid()),
            'rowCount' => self::faker()->numberBetween(1, 25),
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
