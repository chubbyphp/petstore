<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

final class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        return new ErrorHandler(
            $container->get(ResponseInterface::class),
            new ErrorResponseGenerator($container->get('config')['debug'])
        );
    }
}
