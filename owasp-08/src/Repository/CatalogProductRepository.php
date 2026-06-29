<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CatalogProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CatalogProduct>
 */
final class CatalogProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogProduct::class);
    }

    public function findOneBySku(string $sku): ?CatalogProduct
    {
        return $this->findOneBy([
            'sku' => $sku,
        ]);
    }
}
