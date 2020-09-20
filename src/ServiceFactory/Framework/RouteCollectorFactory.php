<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Routing\RouteCollector;

final class RouteCollectorFactory
{
    public function __invoke(ContainerInterface $container): RouteCollector
    {
        return new RouteCollector(
            $container->get(ResponseFactoryInterface::class),
            $container->get(CallableResolverInterface::class),
            $container,
            new RequestHandler(true),
            null,
            $container->get('config')['fastroute']['cache']
        );
    }
}
