<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Chubbyphp\Framework\Router\FastRoute\UrlGenerator;
use Chubbyphp\Framework\Router\RoutesByNameInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Psr\Container\ContainerInterface;

final class UrlGeneratorFactory
{
    public function __invoke(ContainerInterface $container): UrlGeneratorInterface
    {
        return new UrlGenerator(
            $container->get(RoutesByNameInterface::class)
        );
    }
}
