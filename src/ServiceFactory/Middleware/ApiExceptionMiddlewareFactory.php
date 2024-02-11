<?php

declare(strict_types=1);

namespace App\ServiceFactory\Middleware;

use App\Middleware\ApiExceptionMiddleware;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class ApiExceptionMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ApiExceptionMiddleware
    {
        return new ApiExceptionMiddleware(
            $container->get(EncoderInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $container->get('config')['debug'],
            $container->get(LoggerInterface::class),
        );
    }
}
