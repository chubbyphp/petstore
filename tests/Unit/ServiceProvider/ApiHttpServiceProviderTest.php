<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use App\ServiceProvider\ApiHttpServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

/**
 * @covers \App\ServiceProvider\ApiHttpServiceProvider
 *
 * @internal
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
        self::assertArrayHasKey(InvalidParametersFactory::class, $container);

        self::assertInstanceOf(ResponseFactory::class, $container['api-http.response.factory']);
        self::assertInstanceOf(StreamFactory::class, $container['api-http.stream.factory']);
        self::assertInstanceOf(InvalidParametersFactory::class, $container[InvalidParametersFactory::class]);
    }
}
