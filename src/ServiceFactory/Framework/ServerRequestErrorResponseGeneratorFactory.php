<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Response\ServerRequestErrorResponseGenerator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

final class ServerRequestErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container): ServerRequestErrorResponseGenerator
    {
        return new ServerRequestErrorResponseGenerator(
            $container->get(ResponseInterface::class),
            $container->get('config')['debug']
        );
    }
}
