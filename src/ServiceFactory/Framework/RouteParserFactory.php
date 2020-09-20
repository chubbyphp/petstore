<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

final class RouteParserFactory
{
    public function __invoke(ContainerInterface $container): RouteParserInterface
    {
        return $container->get(RouteCollectorInterface::class)->getRouteParser();
    }
}
