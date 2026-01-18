<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var array{debug: bool} $config */
        $config = $container->get('config');

        return new ErrorHandler(
            $responseFactory,
            new ErrorResponseGenerator($config['debug'])
        );
    }
}
