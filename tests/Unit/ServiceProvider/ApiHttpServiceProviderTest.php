<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use App\ServiceProvider\ApiHttpServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @covers \App\ServiceProvider\ApiHttpServiceProvider
 */
final class ApiHttpServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();

        $serviceProvider = new ApiHttpServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('api-http.response.factory', $container);
        self::assertArrayHasKey('api-http.stream.factory', $container);
        self::assertArrayHasKey(ErrorFactory::class, $container);

        self::assertInstanceOf(ResponseFactoryInterface::class, $container['api-http.response.factory']);
        self::assertInstanceOf(StreamFactoryInterface::class, $container['api-http.stream.factory']);
        self::assertInstanceOf(ErrorFactory::class, $container[ErrorFactory::class]);
    }
}
