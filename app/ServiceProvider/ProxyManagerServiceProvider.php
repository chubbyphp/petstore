<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

final class ProxyManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['proxymanager.factory'] = static function () {
            return new LazyLoadingValueHolderFactory();
        };

        $container['proxymanager.doctrine.dbal.connection_registry'] = static function () use ($container) {
            return $container['proxymanager.factory']->createProxy(ConnectionRegistry::class,
                static function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container): void {
                    $wrappedObject = $container['doctrine.dbal.connection_registry'];
                    $initializer = null;
                }
            );
        };

        $container['proxymanager.doctrine.orm.manager_registry'] = static function () use ($container) {
            return $container['proxymanager.factory']->createProxy(ManagerRegistry::class,
                static function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($container): void {
                    $wrappedObject = $container['doctrine.orm.manager_registry'];
                    $initializer = null;
                }
            );
        };
    }
}
