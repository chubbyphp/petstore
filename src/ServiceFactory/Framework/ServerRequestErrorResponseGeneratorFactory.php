<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ServerRequestErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ServerRequestErrorResponseGenerator
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /** @var array{debug: bool} $config */
        $config = $container->get('config');

        return new ServerRequestErrorResponseGenerator(
            $responseFactory,
            $config['debug']
        );
    }
}
