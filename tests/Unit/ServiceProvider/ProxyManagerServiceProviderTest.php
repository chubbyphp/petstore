<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\ProxyManagerServiceProvider;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

/**
 * @covers \App\ServiceProvider\ProxyManagerServiceProvider
 *
 * @internal
 */
final class ProxyManagerServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'doctrine.dbal.connection_registry' => $this->getMockByCalls(ConnectionRegistry::class, [
                Call::create('getDefaultConnectionName')->with()->willReturn('default'),
            ]),
            'doctrine.orm.manager_registry' => $this->getMockByCalls(ManagerRegistry::class, [
                Call::create('getDefaultManagerName')->with()->willReturn('default'),
            ]),
        ]);

        $serviceProvider = new ProxyManagerServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('proxymanager.factory', $container);
        self::assertArrayHasKey('proxymanager.doctrine.dbal.connection_registry', $container);
        self::assertArrayHasKey('proxymanager.doctrine.orm.manager_registry', $container);

        self::assertInstanceOf(LazyLoadingValueHolderFactory::class, $container['proxymanager.factory']);
        self::assertInstanceOf(ConnectionRegistry::class, $container['proxymanager.doctrine.dbal.connection_registry']);
        self::assertInstanceOf(ManagerRegistry::class, $container['proxymanager.doctrine.orm.manager_registry']);

        self::assertSame('default', $container['proxymanager.doctrine.dbal.connection_registry']->getDefaultConnectionName());
        self::assertSame('default', $container['proxymanager.doctrine.orm.manager_registry']->getDefaultManagerName());
    }
}
