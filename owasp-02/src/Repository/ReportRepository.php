<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Report;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    /**
     * @return list<Report>
     */
    public function findLatestByUser(User $user): array
    {
        $result = $this->createQueryBuilder('report')
            ->andWhere('report.user = :user')
            ->setParameter('user', $user)
            ->orderBy('report.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, static fn (mixed $report): bool => $report instanceof Report));
    }
}
