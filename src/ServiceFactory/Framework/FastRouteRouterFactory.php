<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Mezzio\Router\FastRouteRouter;
use Psr\Container\ContainerInterface;

final class FastRouteRouterFactory
{
    public function __invoke(ContainerInterface $container): FastRouteRouter
    {
        /** @var array{fastroute: array{cache: null|string}} */
        $config = $container->get('config');

        $fastrouteConfig = [];

        if (null !== $config['fastroute']['cache']) {
            $fastrouteConfig[FastRouteRouter::CONFIG_CACHE_ENABLED] = true;
            $fastrouteConfig[FastRouteRouter::CONFIG_CACHE_FILE] = $config['fastroute']['cache'];
        }

        return new FastRouteRouter(null, null, $fastrouteConfig);
    }
}
