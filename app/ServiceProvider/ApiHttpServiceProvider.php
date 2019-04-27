<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ApiHttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['api-http.response.factory'] = function () use ($container) {
            return new Psr17Factory();
        };

        $container['api-http.stream.factory'] = function () use ($container) {
            return new Psr17Factory();
        };

        $container[ErrorFactory::class] = function () {
            return new ErrorFactory();
        };
    }
}
