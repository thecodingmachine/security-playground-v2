<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\PaymentHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentHistory>
 */
final class PaymentHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentHistory::class);
    }

    public function countForOrder(Order $order): int
    {
        return (int) $this->createQueryBuilder('payment_history')
            ->select('COUNT(payment_history.id)')
            ->andWhere('payment_history.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
