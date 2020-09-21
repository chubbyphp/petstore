<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Handler\NotFoundHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

final class NotFoundHandlerFactory
{
    public function __invoke(ContainerInterface $container): NotFoundHandler
    {
        return new NotFoundHandler($container->get(ResponseInterface::class));
    }
}
