<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Router\FastRoute\Router;
use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Container\ContainerInterface;

final class RouterFactory
{
    public function __invoke(ContainerInterface $container): Router
    {
        return new Router(
            $container->get(RouteInterface::class.'[]'),
            $container->get('config')['fastroute']['cache']
        );
    }
}
