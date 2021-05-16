<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Middleware\RouteMatcherMiddleware;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class RouteMatcherMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): RouteMatcherMiddleware
    {
        return new RouteMatcherMiddleware(
            $container->get(RouteMatcherInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $container->get(LoggerInterface::class)
        );
    }
}
