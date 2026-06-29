<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;

final class PermissionChecker
{
    public function canExportSensitiveData(User $user, bool $simulateFailure): bool
    {
        if ($simulateFailure) {
            throw new \RuntimeException(sprintf('Permission service timeout for user %s', $user->getUsername()));
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
