<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\ErrorHandlerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Laminas\Stratigility\Middleware\ErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \App\ServiceFactory\Framework\ErrorHandlerFactory
 *
 * @internal
 */
final class ErrorHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseInterface::class)->willReturn(static function () use ($response) {
                return $response;
            }),
            Call::create('get')->with('config')->willReturn(['debug' => true]),
        ]);

        $factory = new ErrorHandlerFactory();

        self::assertInstanceOf(ErrorHandler::class, $factory($container));
    }
}
