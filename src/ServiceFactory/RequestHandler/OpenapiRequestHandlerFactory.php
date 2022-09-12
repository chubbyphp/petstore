<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler;

use App\RequestHandler\OpenapiRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class OpenapiRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): OpenapiRequestHandler
    {
        return new OpenapiRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class)
        );
    }
}
