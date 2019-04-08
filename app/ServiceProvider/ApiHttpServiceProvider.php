<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\ErrorFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zend\Diactoros\ResponseFactory;

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

        $container[ErrorFactory::class] = function () {
            return new ErrorFactory();
        };
    }
}
