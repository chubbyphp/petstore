<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Chubbyphp\Framework\Router\RouteInterface;
use Psr\Container\ContainerInterface;

final class RoutesFactory
{
    /**
     * @return array<RouteInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [];
    }
}
