<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use App\ApiHttp\Factory\ResponseFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ApiHttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['api-http.response.factory'] = function () {
            return new ResponseFactory();
        };

        $container[InvalidParametersFactory::class] = function () {
            return new InvalidParametersFactory();
        };
    }
}
