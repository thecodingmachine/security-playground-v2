<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SafeCatalogImportService
{
    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{sku?:string,name?:string,description?:string}>
     */
    public function sanitizeRows(array $rows): array
    {
        $allowedFields = ['sku', 'name', 'description'];
        $sanitizedRows = [];

        foreach ($rows as $row) {
            $cleanRow = [];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $row) && is_string($row[$field])) {
                    $cleanRow[$field] = $row[$field];
                }
            }

            $sanitizedRows[] = $cleanRow;
        }

        return $sanitizedRows;
    }

    public function assertIntegrity(string $expectedChecksum, string $computedChecksum): void
    {
        if (!hash_equals($expectedChecksum, $computedChecksum)) {
            throw new BadRequestHttpException('Invalid import file integrity.');
        }
    }
}
