<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\RequestHandler;

use App\Core\RequestHandler\PingRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PingRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): PingRequestHandler
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new PingRequestHandler($responseFactory);
    }
}
