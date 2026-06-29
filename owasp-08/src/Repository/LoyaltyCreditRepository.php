<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LoyaltyCredit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoyaltyCredit>
 */
final class LoyaltyCreditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoyaltyCredit::class);
    }
}
