<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ProcessedWebhookEvent;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ProcessedWebhookEvent>
 */
final class ProcessedWebhookEventFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return ProcessedWebhookEvent::class;
    }

    protected function defaults(): array
    {
        return [
            'eventId' => sprintf('evt_%s', self::faker()->uuid()),
            'processedAt' => new \DateTimeImmutable(),
        ];
    }
}
