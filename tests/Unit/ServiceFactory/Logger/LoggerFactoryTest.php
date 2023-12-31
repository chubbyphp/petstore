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
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
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
                'level' => Level::Notice,
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
        self::assertSame(Level::Notice, $streamHandler->getLevel());

        /** @var LogstashFormatter $logstashFormatter */
        $logstashFormatter = $streamHandler->getFormatter();

        self::assertInstanceOf(LogstashFormatter::class, $logstashFormatter);

        $record = new LogRecord(new \DateTimeImmutable('2023-12-31T12:15:00Z'), 'channel', Level::Notice, 'message');

        self::assertStringContainsString('"type":"app"', $logstashFormatter->format($record));
    }
}
