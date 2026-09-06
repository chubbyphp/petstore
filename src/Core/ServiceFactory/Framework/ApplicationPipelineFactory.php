<?php

declare(strict_types=1);

namespace App\Core\ServiceFactory\Framework;

use Laminas\Stratigility\MiddlewarePipe;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class ApplicationPipelineFactory
{
    public function __invoke(ContainerInterface $container): MiddlewarePipeInterface
    {
        /** @var array<MiddlewareInterface> $middlewares */
        $middlewares = $container->get(MiddlewareInterface::class.'[]');

        $pipeline = new MiddlewarePipe();

        foreach ($middlewares as $middleware) {
            $pipeline->pipe($middleware);
        }

        return $pipeline;
    }
}
