<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

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

        $container['api-http.stream.factory'] = function () {
            return new StreamFactory();
        };

        $container[InvalidParametersFactory::class] = function () {
            return new InvalidParametersFactory();
        };
    }
}
