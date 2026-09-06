<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;

final class AppFactory
{
    /**
     * @return App<ContainerInterface>
     */
    public function __invoke(ContainerInterface $container): App
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $container->get(RouteCollectorInterface::class);

        /** @var array<MiddlewareInterface> $middlewares */
        $middlewares = $container->get(MiddlewareInterface::class.'[]');

        $app = new App($responseFactory, $container, $callableResolver, $routeCollector);

        foreach ($middlewares as $middleware) {
            $app->addMiddleware($middleware);
        }

        return $app;
    }
}
