<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Chubbyphp\Cors\CorsMiddleware;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\MiddlewareFactoryInterface;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewaresFactory
{
    /**
     * @return list<MiddlewareInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var MiddlewareFactoryInterface $middlewareFactory */
        $middlewareFactory = $container->get(MiddlewareFactoryInterface::class);

        return [
            $middlewareFactory->lazy(ErrorHandler::class),
            $middlewareFactory->lazy(CorsMiddleware::class),
            $middlewareFactory->lazy(RouteMiddleware::class),
            $middlewareFactory->lazy(MethodNotAllowedMiddleware::class),
            $middlewareFactory->lazy(DispatchMiddleware::class),
            $middlewareFactory->lazy(NotFoundHandler::class),
        ];
    }
}
