<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return list<array{id:int,name:string,description:string,price_cents:int,is_public:int}>
     */
    public function findPublicCatalogVulnerable(string $query): array
    {
        $sql = 'SELECT id, name, description, price_cents, is_public
                FROM product
                WHERE is_public = 1
                AND name LIKE "%'.$query.'%"
                ORDER BY name ASC';

        /** @var list<array{id:int,name:string,description:string,price_cents:int,is_public:int}> $rows */
        $rows = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAllAssociative();

        return $rows;
    }

    /**
     * @return list<array{id:int,name:string,description:string,price_cents:int,is_public:int}>
     */
    public function findPublicCatalogSafe(string $query): array
    {
        $sql = 'SELECT id, name, description, price_cents, is_public
                FROM product
                WHERE is_public = 1
                AND name LIKE :query
                ORDER BY name ASC';

        /** @var list<array{id:int,name:string,description:string,price_cents:int,is_public:int}> $rows */
        $rows = $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'query' => '%'.$query.'%',
        ])->fetchAllAssociative();

        return $rows;
    }

    /**
     * @return list<array{id:int,name:string,type:string}>
     */
    public function findInternalSearchVulnerable(string $query): array
    {
        $sql = 'SELECT id, name, "product" AS type
                FROM product
                WHERE name LIKE "%'.$query.'%"
                ORDER BY name ASC';

        /** @var list<array{id:int,name:string,type:string}> $rows */
        $rows = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAllAssociative();

        return $rows;
    }

    /**
     * @return list<array{id:int,name:string,type:string}>
     */
    public function findInternalSearchSafe(string $query): array
    {
        $sql = 'SELECT id, name, "product" AS type
                FROM product
                WHERE name LIKE :query
                ORDER BY name ASC';

        /** @var list<array{id:int,name:string,type:string}> $rows */
        $rows = $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'query' => '%'.$query.'%',
        ])->fetchAllAssociative();

        return $rows;
    }
}
