<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Framework;

use App\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Mezzio\Response\ServerRequestErrorResponseGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \App\ServiceFactory\Framework\ServerRequestErrorResponseGeneratorFactory
 *
 * @internal
 */
final class ServerRequestErrorResponseGeneratorFactoryTest extends TestCase
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

        $factory = new ServerRequestErrorResponseGeneratorFactory();

        self::assertInstanceOf(ServerRequestErrorResponseGenerator::class, $factory($container));
    }
}
