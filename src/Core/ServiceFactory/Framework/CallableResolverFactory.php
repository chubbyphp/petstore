<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Psr\Container\ContainerInterface;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;

final class CallableResolverFactory
{
    public function __invoke(ContainerInterface $container): CallableResolverInterface
    {
        return new CallableResolver($container);
    }
}
