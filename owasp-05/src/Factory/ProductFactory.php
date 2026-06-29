<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Product::class;
    }

    protected function defaults(): array
    {
        return [
            'owner' => UserFactory::new(),
            'name' => self::faker()->words(3, true),
            'description' => self::faker()->sentence(12),
            'priceCents' => self::faker()->numberBetween(1000, 99000),
            'isPublic' => true,
        ];
    }
}
