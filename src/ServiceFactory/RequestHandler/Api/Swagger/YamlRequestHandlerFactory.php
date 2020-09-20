<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api\Swagger;

use App\RequestHandler\Api\Swagger\YamlRequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class YamlRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): YamlRequestHandler
    {
        return new YamlRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class)
        );
    }
}
