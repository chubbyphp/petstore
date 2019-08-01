<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\SlimServiceProvider;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\CallableResolver;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;

/**
 * @covers \App\ServiceProvider\SlimServiceProvider
 *
 * @internal
 */
final class SlimServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'api-http.response.factory' => $this->getMockByCalls(ResponseFactoryInterface::class),
            'routerCacheFile' => sys_get_temp_dir().'/routerCacheFile.php',
        ]);

        $serviceProvider = new SlimServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(PsrContainer::class, $container);
        self::assertArrayHasKey(CallableResolver::class, $container);
        self::assertArrayHasKey(RouteCollector::class, $container);
        self::assertArrayHasKey('router', $container);

        self::assertInstanceOf(PsrContainer::class, $container[PsrContainer::class]);
        self::assertInstanceOf(CallableResolver::class, $container[CallableResolver::class]);
        self::assertInstanceOf(RouteCollector::class, $container[RouteCollector::class]);
        self::assertInstanceOf(RouteParser::class, $container['router']);
    }
}
