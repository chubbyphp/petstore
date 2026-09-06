<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Mezzio\Router\Route;
use Psr\Container\ContainerInterface;

final class RoutesFactory
{
    /**
     * @return array<Route>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [];
    }
}
