<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Middleware;

use App\Middleware\ApiExceptionMiddleware;
use App\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\ServiceFactory\Middleware\ApiExceptionMiddlewareFactory
 *
 * @internal
 */
final class ApiExceptionMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var EncoderInterface $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var LoggerInterface $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EncoderInterface::class)->willReturn($encoder),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
            Call::create('get')->with('config')->willReturn(['debug' => true]),
            Call::create('get')->with(LoggerInterface::class)->willReturn($logger),
        ]);

        $factory = new ApiExceptionMiddlewareFactory();

        self::assertInstanceOf(ApiExceptionMiddleware::class, $factory($container));
    }
}
