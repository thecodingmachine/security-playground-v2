<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SalesOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesOrder>
 */
class SalesOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesOrder::class);
    }

    /**
     * @return list<SalesOrder>
     */
    public function findByOwner(User $owner): array
    {
        /** @var list<SalesOrder> $orders */
        $orders = $this->createQueryBuilder('sales_order')
            ->andWhere('sales_order.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('sales_order.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $orders;
    }

    public function findOneForOwnerById(int $id, User $owner): ?SalesOrder
    {
        /** @var SalesOrder|null $order */
        $order = $this->createQueryBuilder('sales_order')
            ->andWhere('sales_order.id = :id')
            ->andWhere('sales_order.owner = :owner')
            ->setParameter('id', $id)
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getOneOrNullResult();

        return $order;
    }
}
