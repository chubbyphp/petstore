<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Mezzio\Router\Route;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouteCollectorInterface;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

final class RouteCollectorFactory
{
    public function __invoke(ContainerInterface $container): RouteCollectorInterface
    {
        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);

        /** @var array<Route> $routes */
        $routes = $container->get(Route::class.'[]');

        $routeCollector = new RouteCollector($router);

        foreach ($routes as $route) {
            $routeCollector->route(
                $route->getPath(),
                $route->getMiddleware(),
                $route->getAllowedMethods(),
                $route->getName()
            );
        }

        return $routeCollector;
    }
}
