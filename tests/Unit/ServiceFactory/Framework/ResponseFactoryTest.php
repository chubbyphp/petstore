<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\ResponseFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \App\ServiceFactory\Framework\ResponseFactory
 *
 * @internal
 */
final class ResponseFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = new ResponseFactory();

        self::assertInstanceOf(ResponseInterface::class, $factory($container)());
    }
}
