<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['negotiator.acceptNegotiator.values'] = static function () use ($container) {
            return $container['serializer']->getContentTypes();
        };

        $container['negotiator.contentTypeNegotiator.values'] = static function () use ($container) {
            return $container['deserializer']->getContentTypes();
        };
    }
}
