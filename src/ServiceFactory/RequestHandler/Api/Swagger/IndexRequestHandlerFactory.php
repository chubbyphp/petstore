<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Swagger;

use App\RequestHandler\Api\Swagger\IndexRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class IndexRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): IndexRequestHandler
    {
        return new IndexRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class)
        );
    }
}
