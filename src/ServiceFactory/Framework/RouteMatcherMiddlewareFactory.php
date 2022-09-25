<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Psr\Container\ContainerInterface;

final class RouteMatcherMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): RouteMatcherMiddleware
    {
        return new RouteMatcherMiddleware($container->get(RouteMatcherInterface::class));
    }
}
