<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ResponseFactory
{
    public function __invoke(ContainerInterface $container): \Closure
    {
        return static function () use ($container) {
            /** @var ResponseFactoryInterface $responseFactory */
            $responseFactory = $container->get(ResponseFactoryInterface::class);

            return $responseFactory->createResponse();
        };
    }
}
