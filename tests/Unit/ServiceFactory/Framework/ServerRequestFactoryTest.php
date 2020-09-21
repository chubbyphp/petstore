<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\ServerRequestFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\ServiceFactory\Framework\ServerRequestFactory
 *
 * @internal
 */
final class ServerRequestFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $factory = new ServerRequestFactory();

        self::assertInstanceOf(ServerRequestInterface::class, $factory()());
    }
}
