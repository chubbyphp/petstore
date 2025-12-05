<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Routing\RouteCollector;

final class RouteCollectorFactory
{
    public function __invoke(ContainerInterface $container): RouteCollectorInterface
    {
        return new RouteCollector(
            $container->get(ResponseFactoryInterface::class),
            $container->get(CallableResolverInterface::class),
            $container,
            $container->get(InvocationStrategyInterface::class),
            null,
            $container->get('config')['fastroute']['cache']
        );
    }
}
