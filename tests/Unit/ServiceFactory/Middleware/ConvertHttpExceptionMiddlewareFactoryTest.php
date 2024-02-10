<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Middleware;

use App\Middleware\ConvertHttpExceptionMiddleware;
use App\ServiceFactory\Middleware\ConvertHttpExceptionMiddlewareFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ServiceFactory\Middleware\ConvertHttpExceptionMiddlewareFactory
 *
 * @internal
 */
final class ConvertHttpExceptionMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $factory = new ConvertHttpExceptionMiddlewareFactory();

        self::assertInstanceOf(ConvertHttpExceptionMiddleware::class, $factory());
    }
}
