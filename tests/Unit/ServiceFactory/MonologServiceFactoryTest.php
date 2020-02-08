<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\MonologServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\Handler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @covers \App\ServiceFactory\MonologServiceFactory
 *
 * @internal
 */
final class MonologServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new MonologServiceFactory())();

        self::assertCount(2, $factories);
    }

    public function testLoggerInterface(): void
    {
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('monolog')->willReturn([
                'name' => 'app',
                'path' => '/var/log',
                'level' => LogLevel::ERROR,
            ]),
        ]);

        $factories = (new MonologServiceFactory())();

        self::assertArrayHasKey(LoggerInterface::class, $factories);

        /** @var Logger $logger */
        $logger = $factories[LoggerInterface::class]($container);

        self::assertInstanceOf(Logger::class, $logger);

        /** @var array<int, Handler> $handlers */
        $handlers = $logger->getHandlers();

        self::assertCount(1, $handlers);

        /** @var BufferHandler $bufferedHandler */
        $bufferedHandler = array_shift($handlers);

        self::assertInstanceOf(BufferHandler::class, $bufferedHandler);

        $handlerReflectionProperty = new \ReflectionProperty($bufferedHandler, 'handler');
        $handlerReflectionProperty->setAccessible(true);

        /** @var StreamHandler $streamHandler */
        $streamHandler = $handlerReflectionProperty->getValue($bufferedHandler);

        self::assertInstanceOf(StreamHandler::class, $streamHandler);

        self::assertSame('/var/log', $streamHandler->getUrl());
        self::assertSame(400, $streamHandler->getLevel());

        /** @var LogstashFormatter $formatter */
        $formatter = $streamHandler->getFormatter();

        self::assertInstanceOf(LogstashFormatter::class, $formatter);
    }

    public function testLogger(): void
    {
        /** @var LoggerInterface|MockObject $container */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(LoggerInterface::class)->willReturn($logger),
        ]);

        $factories = (new MonologServiceFactory())();

        self::assertArrayHasKey('logger', $factories);

        self::assertInstanceOf(
            LoggerInterface::class,
            $factories['logger']($container)
        );
    }
}
