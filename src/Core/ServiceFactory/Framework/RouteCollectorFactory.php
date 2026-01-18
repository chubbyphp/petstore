<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

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
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        /** @var InvocationStrategyInterface $invocationStrategy */
        $invocationStrategy = $container->get(InvocationStrategyInterface::class);

        /** @var array{fastroute: array{cache: null|string}} $config */
        $config = $container->get('config');

        return new RouteCollector(
            $responseFactory,
            $callableResolver,
            $container,
            $invocationStrategy,
            null,
            $config['fastroute']['cache']
        );
    }
}
