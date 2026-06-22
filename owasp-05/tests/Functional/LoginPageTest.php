<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\TestCase;

final class LoginPageTest extends TestCase
{
    public function testKernelClassExists(): void
    {
        self::assertTrue(class_exists(\App\Kernel::class));
    }
}
