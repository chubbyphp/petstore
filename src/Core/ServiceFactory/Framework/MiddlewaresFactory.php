<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use App\Core\Middleware\ConvertHttpExceptionMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Middleware\ErrorMiddleware;

final class MiddlewaresFactory
{
    /**
     * @return list<MiddlewareInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var CorsMiddleware $corsMiddleware */
        $corsMiddleware = $container->get(CorsMiddleware::class);

        /** @var ConvertHttpExceptionMiddleware $convertHttpExceptionMiddleware */
        $convertHttpExceptionMiddleware = $container->get(ConvertHttpExceptionMiddleware::class);

        /** @var ErrorMiddleware $errorMiddleware */
        $errorMiddleware = $container->get(ErrorMiddleware::class);

        return [
            $corsMiddleware,
            $convertHttpExceptionMiddleware,
            $errorMiddleware,
        ];
    }
}
