<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Routing\RouteCollectorProxy;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): RouteCollectorInterface
    {
        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $callback();

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        $routes = new RouteCollectorProxy($responseFactory, $callableResolver, $container, $routeCollector);

        $routes->get('/ping', PingRequestHandler::class)->setName('ping');
        $routes->get('/openapi', OpenapiRequestHandler::class)->setName('openapi');

        return $routeCollector;
    }
}
