<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @return list<Invoice>
     */
    public function findByUser(User $user): array
    {
        $result = $this->createQueryBuilder('invoice')
            ->andWhere('invoice.user = :user')
            ->setParameter('user', $user)
            ->orderBy('invoice.issuedAt', 'DESC')
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, static fn (mixed $invoice): bool => $invoice instanceof Invoice));
    }
}
