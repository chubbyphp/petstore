<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler;

use App\ServiceFactory\Logger\LoggerFactory;
use App\Tests\Helper\AssertHelper;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \App\ServiceFactory\Logger\LoggerFactory
 *
 * @internal
 */
final class LoggerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $path = sys_get_temp_dir().'/'.uniqid('logger-factory-').'.log';

        $config = [
            'monolog' => [
                'name' => 'test',
                'path' => $path,
                'level' => Logger::NOTICE,
            ],
        ];

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn($config),
        ]);

        $factory = new LoggerFactory();

        /** @var Logger $logger */
        $logger = $factory($container);

        self::assertInstanceOf(LoggerInterface::class, $logger);
        self::assertInstanceOf(Logger::class, $logger);

        $handlers = $logger->getHandlers();

        self::assertCount(1, $handlers);

        /** @var BufferHandler $bufferHandler */
        $bufferHandler = array_shift($handlers);

        self::assertInstanceOf(BufferHandler::class, $bufferHandler);

        /** @var StreamHandler $streamHandler */
        $streamHandler = AssertHelper::readProperty('handler', $bufferHandler);

        self::assertInstanceOf(StreamHandler::class, $streamHandler);

        self::assertSame($path, $streamHandler->getUrl());
        self::assertSame(Logger::NOTICE, $streamHandler->getLevel());

        /** @var LogstashFormatter $logstashFormatter */
        $logstashFormatter = $streamHandler->getFormatter();

        self::assertInstanceOf(LogstashFormatter::class, $logstashFormatter);

        self::assertStringContainsString('"type":"app"', $logstashFormatter->format([]));
    }
}
