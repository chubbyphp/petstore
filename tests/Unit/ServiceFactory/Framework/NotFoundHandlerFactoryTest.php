<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\NotFoundHandlerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Mezzio\Handler\NotFoundHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \App\ServiceFactory\Framework\NotFoundHandlerFactory
 *
 * @internal
 */
final class NotFoundHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseInterface::class)->willReturn(static fn () => $response),
        ]);

        $factory = new NotFoundHandlerFactory();

        self::assertInstanceOf(NotFoundHandler::class, $factory($container));
    }
}
