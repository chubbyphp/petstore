<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Middleware;

use App\Core\Middleware\ApiExceptionMiddleware;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;

final class ApiExceptionMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ApiExceptionMiddleware
    {
        /** @var EncoderInterface $encoder */
        $encoder = $container->get(EncoderInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var array{debug: bool} $config */
        $config = $container->get('config');

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        return new ApiExceptionMiddleware(
            $encoder,
            $responseFactory,
            $config['debug'],
            $logger,
        );
    }
}
