<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Psr\Container\ContainerInterface;

final class ProxyManagerServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'proxymanager.factory' => static function () {
                return new LazyLoadingValueHolderFactory();
            },
            'proxymanager.doctrine.dbal.connection_registry' => static function (ContainerInterface $container) {
                return $container->get('proxymanager.factory')->createProxy(ConnectionRegistry::class,
                    static function (
                        &$wrappedObject,
                        $proxy,
                        $method,
                        $parameters,
                        &$initializer
                    ) use ($container): void {
                        $wrappedObject = $container->get('doctrine.dbal.connection_registry');
                        $initializer = null;
                    }
                );
            },
            'proxymanager.doctrine.orm.manager_registry' => static function (ContainerInterface $container) {
                return $container->get('proxymanager.factory')->createProxy(ManagerRegistry::class,
                    static function (
                        &$wrappedObject,
                        $proxy,
                        $method,
                        $parameters,
                        &$initializer
                    ) use ($container): void {
                        $wrappedObject = $container->get('doctrine.orm.manager_registry');
                        $initializer = null;
                    }
                );
            },
        ];
    }
}
