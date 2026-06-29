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
    public function findByOwner(User $owner): array
    {
        /** @var list<Report> $reports */
        $reports = $this->createQueryBuilder('report')
            ->andWhere('report.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('report.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $reports;
    }

    /** @return list<array{title:string,owner:string,created_at:\DateTimeInterface|string}> */
    public function findSensitiveExportRows(): array
    {
        $query = $this->createQueryBuilder('report')
            ->select('report.title AS title, owner.username AS owner, report.createdAt AS created_at')
            ->innerJoin('report.owner', 'owner')
            ->andWhere('report.isSensitive = :sensitive')
            ->setParameter('sensitive', true)
            ->orderBy('report.id', 'ASC')
            ->getQuery();

        /** @var list<array{title:string,owner:string,created_at:\DateTimeInterface|string}> $rows */
        $rows = $query->getArrayResult();

        return $rows;
    }
}
