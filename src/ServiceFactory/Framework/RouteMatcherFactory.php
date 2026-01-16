<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Router\FastRoute\RouteMatcher;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Container\ContainerInterface;

final class RouteMatcherFactory
{
    public function __invoke(ContainerInterface $container): RouteMatcherInterface
    {
        /** @var RoutesByNameInterface $routes */
        $routes = $container->get(RoutesByNameInterface::class);

        /** @var array{fastroute: array{cache: null|string}} $config */
        $config = $container->get('config');

        return new RouteMatcher(
            $routes,
            $config['fastroute']['cache']
        );
    }
}
