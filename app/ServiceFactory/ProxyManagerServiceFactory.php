<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Repository\UserRepository;
use App\Security\Authentication\PasswordManager;
use App\Security\Authentication\PasswordManagerInterface;
use App\Security\UserRepositoryInterface;
use Chubbyphp\Validation\ValidatorInterface;
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
            'proxymanager.doctrine.dbal.connection_registry' => $this->getProxy(
                ConnectionRegistry::class,
                'doctrine.dbal.connection_registry'
            ),
            'proxymanager.doctrine.orm.manager_registry' => $this->getProxy(
                ManagerRegistry::class,
                'doctrine.orm.manager_registry'
            ),
            'proxymanager.'.PasswordManager::class => $this->getProxy(
                PasswordManagerInterface::class,
                PasswordManager::class
            ),
            'proxymanager.'.UserRepository::class => $this->getProxy(
                UserRepositoryInterface::class,
                UserRepository::class
            ),
            'proxymanager.validator' => $this->getProxy(
                ValidatorInterface::class,
                'validator'
            ),
        ];
    }

    private function getProxy(string $class, string $id): \Closure
    {
        return static function (ContainerInterface $container) use ($class, $id) {
            return $container->get('proxymanager.factory')->createProxy($class,
                static function (
                    &$wrappedObject,
                    $proxy,
                    $method,
                    $parameters,
                    &$initializer
                ) use ($container, $id): void {
                    $wrappedObject = $container->get($id);
                    $initializer = null;
                }
            );
        };
    }
}
