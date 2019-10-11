<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\MonologServiceProvider;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\MonologServiceProvider
 *
 * @internal
 */
final class MonologServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container([
            'monolog' => [
                'name' => 'petstore',
                'path' => '/pah/to/log/file',
                'level' => Logger::DEBUG,
            ],
        ]);

        $serviceProvider = new MonologServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(Logger::class, $container);
        self::assertArrayHasKey('logger', $container);

        self::assertInstanceOf(Logger::class, $container[Logger::class]);
        self::assertInstanceOf(Logger::class, $container['logger']);

        self::assertSame($container[Logger::class], $container['logger']);

        /** @var Logger $logger */
        $logger = $container['logger'];

        self::assertSame('petstore', $logger->getName());

        $handlers = $logger->getHandlers();

        self::assertCount(1, $handlers);

        /** @var HandlerInterface $handler */
        $handler = array_shift($handlers);

        self::assertInstanceOf(StreamHandler::class, $handler);

        self::assertInstanceOf(LogstashFormatter::class, $handler->getFormatter());
    }
}
