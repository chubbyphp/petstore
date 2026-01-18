<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Framework;

use App\Core\ServiceFactory\Framework\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\ServiceFactory\Framework\ServerRequestFactory
 *
 * @internal
 */
final class ServerRequestFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new ServerRequestFactory();

        self::assertInstanceOf(ServerRequestInterface::class, $factory()());
    }
}
