<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use App\ApiHttp\Factory\InvalidParametersFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiHttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['api-http.response.factory'] = function () {
            // this dirty hack is needed, cause empty '' as a response argument results in '' on response
            return new class() implements ResponseFactoryInterface
            {
                public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
                {
                    return new Response($code, [], null, '1.1', '' !== $reasonPhrase ? $reasonPhrase : null);
                }
            };
        };

        $container['api-http.stream.factory'] = function () {
            return new Psr17Factory();
        };

        $container[InvalidParametersFactory::class] = function () {
            return new InvalidParametersFactory();
        };
    }
}
