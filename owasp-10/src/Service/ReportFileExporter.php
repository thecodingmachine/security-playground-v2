<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Report;

final class ReportFileExporter
{
    public function generate(Report $report, bool $simulateFailure): string
    {
        if ($simulateFailure || $report->isBroken()) {
            throw new \RuntimeException(sprintf('Unable to generate report from %s', $report->getStoragePath()));
        }

        return sprintf(
            "title;owner;sensitive\n%s;%s;%s\n",
            $report->getTitle(),
            $report->getOwner()->getUsername(),
            $report->isSensitive() ? 'yes' : 'no'
        );
    }
}
