<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class MiddlewareServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container[AcceptAndContentTypeMiddleware::class] = static function () use ($container) {
            return new AcceptAndContentTypeMiddleware(
                $container['negotiator.acceptNegotiator'],
                $container['negotiator.contentTypeNegotiator'],
                $container['api-http.response.manager']
            );
        };

        $container['allowOrigins'] = static function () use ($container) {
            $allowOrigins = [];
            foreach ($container['cors']['allow-origin'] as $allowOrigin => $class) {
                $allowOrigins[] = new $class($allowOrigin);
            }

            return $allowOrigins;
        };

        $container[CorsMiddleware::class] = static function () use ($container) {
            return new CorsMiddleware(
                $container['api-http.response.factory'],
                new OriginNegotiator($container['allowOrigins']),
                new MethodNegotiator($container['cors']['allow-methods']),
                new HeadersNegotiator($container['cors']['allow-headers']),
                $container['cors']['expose-headers'],
                $container['cors']['allow-credentials'],
                $container['cors']['max-age']
            );
        };
    }
}
