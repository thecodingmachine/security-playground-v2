<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\CatalogProduct;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CatalogProduct>
 */
final class CatalogProductFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CatalogProduct::class;
    }

    protected function defaults(): array
    {
        return [
            'sku' => strtoupper(self::faker()->bothify('SKU-###??')),
            'name' => self::faker()->words(3, true),
            'description' => self::faker()->sentence(),
            'priceCents' => self::faker()->numberBetween(1000, 100000),
            'discountPercent' => 0,
            'isPublic' => true,
            'isFeatured' => false,
            'status' => 'draft',
        ];
    }
}
