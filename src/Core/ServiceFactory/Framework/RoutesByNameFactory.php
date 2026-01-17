<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RoutesByName;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Psr\Container\ContainerInterface;

final class RoutesByNameFactory
{
    public function __invoke(ContainerInterface $container): RoutesByNameInterface
    {
        /** @var array<RouteInterface> $routes */
        $routes = $container->get(RouteInterface::class.'[]');

        return new RoutesByName($routes);
    }
}
