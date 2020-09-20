<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api;

use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use App\ServiceFactory\RequestHandler\Api\Swagger\IndexRequestHandlerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\Swagger\IndexRequestHandlerFactory
 *
 * @internal
 */
final class IndexRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var StreamFactoryInterface $stream */
        $stream = $this->getMockByCalls(StreamFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
            Call::create('get')->with(StreamFactoryInterface::class)->willReturn($stream),
        ]);

        $factory = new IndexRequestHandlerFactory();

        self::assertInstanceOf(IndexRequestHandler::class, $factory($container));
    }
}
