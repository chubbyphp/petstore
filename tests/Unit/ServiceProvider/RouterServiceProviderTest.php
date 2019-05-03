<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\RouterServiceProvider;
use Chubbyphp\Framework\Router\FastRouteRouter;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\RouterServiceProvider
 */
final class RouterServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container([
            'routerCacheFile' => tempnam(sys_get_temp_dir(), 'fast-route-') .'.php',
        ]);

        $serviceProvider = new RouterServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(FastRouteRouter::class, $container);

        self::assertInstanceOf(FastRouteRouter::class, $container[FastRouteRouter::class]);
    }
}
