<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\ProxyManagerServiceFactory;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\VirtualProxyInterface;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\ProxyManagerServiceFactory
 *
 * @internal
 */
final class ProxyManagerServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new ProxyManagerServiceFactory())();

        self::assertCount(3, $factories);
    }

    public function testFactory(): void
    {
        $factories = (new ProxyManagerServiceFactory())();

        self::assertArrayHasKey('proxymanager.factory', $factories);

        self::assertInstanceOf(LazyLoadingValueHolderFactory::class, $factories['proxymanager.factory']());
    }

    public function testConnectionRegistry(): void
    {
        /** @var ConnectionRegistry|MockObject $connectionRegistry */
        $connectionRegistry = $this->getMockByCalls(ConnectionRegistry::class);

        /** @var VirtualProxyInterface|MockObject $virtualProxy */
        $virtualProxy = $this->getMockByCalls(VirtualProxyInterface::class);

        /** @var LazyLoadingValueHolderFactory|MockObject $proxyManagerFactory */
        $proxyManagerFactory = $this->getMockByCalls(LazyLoadingValueHolderFactory::class, [
            Call::create('createProxy')
                ->with(
                    ConnectionRegistry::class,
                    new ArgumentCallback(function (\Closure $callback) use ($connectionRegistry): void {
                        $wrappedObject = false;
                        $initializer = false;

                        $callback($wrappedObject, null, null, null, $initializer);

                        self::assertSame($connectionRegistry, $wrappedObject);
                        self::assertNull($initializer);
                    }),
                    []
                )
                ->willReturn($virtualProxy),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('proxymanager.factory')->willReturn($proxyManagerFactory),
            Call::create('get')->with('doctrine.dbal.connection_registry')->willReturn($connectionRegistry),
        ]);

        $factories = (new ProxyManagerServiceFactory())();

        self::assertArrayHasKey('proxymanager.doctrine.dbal.connection_registry', $factories);

        self::assertSame(
            $virtualProxy,
            $factories['proxymanager.doctrine.dbal.connection_registry']($container)
        );
    }

    public function testManagerRegistry(): void
    {
        /** @var ManagerRegistry|MockObject $managerRegistry */
        $managerRegistry = $this->getMockByCalls(ManagerRegistry::class);

        /** @var VirtualProxyInterface|MockObject $virtualProxy */
        $virtualProxy = $this->getMockByCalls(VirtualProxyInterface::class);

        /** @var LazyLoadingValueHolderFactory|MockObject $proxyManagerFactory */
        $proxyManagerFactory = $this->getMockByCalls(LazyLoadingValueHolderFactory::class, [
            Call::create('createProxy')
                ->with(
                    ManagerRegistry::class,
                    new ArgumentCallback(function (\Closure $callback) use ($managerRegistry): void {
                        $wrappedObject = false;
                        $initializer = false;

                        $callback($wrappedObject, null, null, null, $initializer);

                        self::assertSame($managerRegistry, $wrappedObject);
                        self::assertNull($initializer);
                    }),
                    []
                )
                ->willReturn($virtualProxy),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('proxymanager.factory')->willReturn($proxyManagerFactory),
            Call::create('get')->with('doctrine.orm.manager_registry')->willReturn($managerRegistry),
        ]);

        $factories = (new ProxyManagerServiceFactory())();

        self::assertArrayHasKey('proxymanager.doctrine.orm.manager_registry', $factories);

        self::assertSame(
            $virtualProxy,
            $factories['proxymanager.doctrine.orm.manager_registry']($container)
        );
    }
}
