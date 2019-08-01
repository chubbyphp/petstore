<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\ServiceProviderInterface;
use Slim\CallableResolver;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollector;

final class SlimServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[PsrContainer::class] = function () use ($container) {
            return new PsrContainer($container);
        };

        $container[CallableResolver::class] = function () use ($container) {
            return new CallableResolver($container[PsrContainer::class]);
        };

        $container[RouteCollector::class] = function () use ($container) {
            $routeCollector = new RouteCollector(
                $container['api-http.response.factory'],
                $container[CallableResolver::class],
                $container[PsrContainer::class]
            );

            $routeCollector->setDefaultInvocationStrategy(new RequestHandler(true));

            if (null !== $routerCacheFile = $container['routerCacheFile']) {
                $routeCollector->setCacheFile($routerCacheFile);
            }

            return $routeCollector;
        };

        $container['router'] = function () use ($container) {
            return $container[RouteCollector::class]->getRouteParser();
        };
    }
}
