<?php

declare(strict_types=1);

namespace App\ServiceProvider;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class MonologServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container[Logger::class] = function () use ($container) {
            return new Logger($container['monolog']['name'], [
                (new StreamHandler(
                    $container['monolog']['path'],
                    $container['monolog']['level']
                ))->setFormatter(new LogstashFormatter('app')),
            ]);
        };

        $container['logger'] = function () use ($container) {
            return $container[Logger::class];
        };
    }
}
