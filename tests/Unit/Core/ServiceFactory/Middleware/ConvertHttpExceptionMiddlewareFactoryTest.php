<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Middleware;

use App\Core\Middleware\ConvertHttpExceptionMiddleware;
use App\Core\ServiceFactory\Middleware\ConvertHttpExceptionMiddlewareFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Core\ServiceFactory\Middleware\ConvertHttpExceptionMiddlewareFactory
 *
 * @internal
 */
final class ConvertHttpExceptionMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new ConvertHttpExceptionMiddlewareFactory();

        self::assertInstanceOf(ConvertHttpExceptionMiddleware::class, $factory());
    }
}
