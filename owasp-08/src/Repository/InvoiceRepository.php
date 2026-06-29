<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
final class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function nextSequenceForOrder(Order $order): int
    {
        $count = (int) $this->createQueryBuilder('invoice')
            ->select('COUNT(invoice.id)')
            ->andWhere('invoice.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getSingleScalarResult();

        return $count + 1;
    }
}
