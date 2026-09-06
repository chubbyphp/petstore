<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Route;
use Psr\Container\ContainerInterface;

final class RoutesDelegator
{
    /**
     * @return array<Route>
     */
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): array
    {
        /** @var array<Route> $routes */
        $routes = $callback();

        /** @var MiddlewareFactoryInterface $middlewareFactory */
        $middlewareFactory = $container->get(MiddlewareFactoryInterface::class);

        $ping = $middlewareFactory->lazy(PingRequestHandler::class);
        $openApi = $middlewareFactory->lazy(OpenapiRequestHandler::class);

        return [
            ...$routes,
            new Route('/ping', $ping, ['GET'], 'ping'),
            new Route('/openapi', $openApi, ['GET'], 'openapi'),
        ];
    }
}
