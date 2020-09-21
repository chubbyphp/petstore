<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Router\FastRouteRouter;
use Psr\Container\ContainerInterface;

final class FastRouteRouterFactory
{
    public function __invoke(ContainerInterface $container): FastRouteRouter
    {
        $config = $container->get('config')['fastroute'];

        $fastrouteConfig = [];

        if (null !== $config['cache']) {
            $fastrouteConfig[FastRouteRouter::CONFIG_CACHE_ENABLED] = true;
            $fastrouteConfig[FastRouteRouter::CONFIG_CACHE_FILE] = $config['cache'];
        }

        return new FastRouteRouter(null, null, $fastrouteConfig);
    }
}
