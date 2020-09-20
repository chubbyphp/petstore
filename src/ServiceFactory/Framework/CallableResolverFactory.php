<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Slim\CallableResolver;

final class CallableResolverFactory
{
    public function __invoke(ContainerInterface $container): CallableResolver
    {
        return new CallableResolver($container);
    }
}
