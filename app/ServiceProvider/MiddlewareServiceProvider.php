<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Chubbyphp\ApiHttp\Middleware\AcceptAndContentTypeMiddleware;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class MiddlewareServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container[AcceptAndContentTypeMiddleware::class] = function () use ($container) {
            return new AcceptAndContentTypeMiddleware(
                $container['negotiator.acceptNegotiator'],
                $container['negotiator.contentTypeNegotiator'],
                $container['api-http.response.manager']
            );
        };
    }
}
