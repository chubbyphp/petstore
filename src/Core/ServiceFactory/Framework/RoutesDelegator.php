<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use App\Core\RequestHandler\OpenapiRequestHandler;
use App\Core\RequestHandler\PingRequestHandler;
use Chubbyphp\Framework\RequestHandler\LazyRequestHandler;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Container\ContainerInterface;

final class RoutesDelegator
{
    /**
     * @return array<RouteInterface>
     */
    public function __invoke(ContainerInterface $container, mixed $_, callable $callback): array
    {
        /** @var array<RouteInterface> $routes */
        $routes = $callback();

        $ping = new LazyRequestHandler($container, PingRequestHandler::class);
        $openApi = new LazyRequestHandler($container, OpenapiRequestHandler::class);

        return [
            ...$routes,
            Route::get('/ping', 'ping', $ping),
            Route::get('/openapi', 'openapi', $openApi),
        ];
    }
}
