<?php

declare(strict_types=1);

namespace App\ServiceFactory\RequestHandler\Api;

use App\RequestHandler\Api\PingRequestHandler;
use Chubbyphp\Serialization\SerializerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class PingRequestHandlerFactory
{
    public function __invoke(ContainerInterface $container): PingRequestHandler
    {
        return new PingRequestHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(SerializerInterface::class)
        );
    }
}
