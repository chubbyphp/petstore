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
        return new RouteMatcher(
            $container->get(RoutesByNameInterface::class),
            $container->get('config')['fastroute']['cache']
        );
    }
}
