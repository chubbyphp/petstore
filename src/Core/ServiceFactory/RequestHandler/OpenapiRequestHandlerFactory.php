<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\RequestHandler;

use App\Core\RequestHandler\OpenapiRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class OpenapiRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): OpenapiRequestHandler
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var StreamFactoryInterface $streamFactory */
        $streamFactory = $container->get(StreamFactoryInterface::class);

        return new OpenapiRequestHandler($responseFactory, $streamFactory);
    }
}
