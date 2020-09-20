<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\LazyMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewaresFactory
{
    /**
     * @return array<int, MiddlewareInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [
            new LazyMiddleware($container, ExceptionMiddleware::class),
            new LazyMiddleware($container, CorsMiddleware::class),
            new LazyMiddleware($container, RouterMiddleware::class),
        ];
    }
}
