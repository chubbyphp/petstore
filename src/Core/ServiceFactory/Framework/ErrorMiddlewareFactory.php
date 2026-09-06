<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Middleware\ErrorMiddleware;

final class ErrorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ErrorMiddleware
    {
        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var array{debug: bool} $config */
        $config = $container->get('config');

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        return new ErrorMiddleware(
            $callableResolver,
            $responseFactory,
            $config['debug'],
            true,
            true,
            $logger
        );
    }
}
