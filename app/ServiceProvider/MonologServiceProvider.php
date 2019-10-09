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
    public function register(Container $container): void
    {
        $container[Logger::class] = static function () use ($container) {
            return new Logger($container['monolog']['name'], [
                (new StreamHandler(
                    $container['monolog']['path'],
                    $container['monolog']['level']
                ))->setFormatter(new LogstashFormatter('app')),
            ]);
        };

        $container['logger'] = static function () use ($container) {
            return $container[Logger::class];
        };
    }
}
