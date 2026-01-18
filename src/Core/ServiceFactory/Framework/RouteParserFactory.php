<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

final class RouteParserFactory
{
    public function __invoke(ContainerInterface $container): RouteParserInterface
    {
        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $container->get(RouteCollectorInterface::class);

        return $routeCollector->getRouteParser();
    }
}
