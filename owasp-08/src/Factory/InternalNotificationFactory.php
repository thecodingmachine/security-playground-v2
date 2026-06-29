<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\InternalNotification;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<InternalNotification>
 */
final class InternalNotificationFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return InternalNotification::class;
    }

    protected function defaults(): array
    {
        return [
            'order' => OrderFactory::new(),
            'message' => 'Notification interne',
            'createdAt' => new \DateTimeImmutable(),
        ];
    }
}
