<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class NotFoundHandlerFactory
{
    public function __invoke(ContainerInterface $container): NotFoundHandler
    {
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new NotFoundHandler($responseFactory);
    }
}
