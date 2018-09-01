<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use App\ApiHttp\Factory\ResponseFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ApiHttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['api-http.response.factory'] = function () {
            return new ResponseFactory();
        };

        $container[ErrorFactory::class] = function () {
            return new ErrorFactory();
        };
    }
}
