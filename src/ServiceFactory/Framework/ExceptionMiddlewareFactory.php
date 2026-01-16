<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class ExceptionMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ExceptionMiddleware
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var array{debug: bool} $config */
        $config = $container->get('config');

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        return new ExceptionMiddleware(
            $responseFactory,
            $config['debug'],
            $logger
        );
    }
}
