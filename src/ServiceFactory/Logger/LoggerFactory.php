<?php

declare(strict_types=1);

namespace App\ServiceFactory\Logger;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        /** @var array{monolog: array{name: string, path: string, level: Level}} $config */
        $config = $container->get('config');

        $monologConfig = $config['monolog'];

        return new Logger($monologConfig['name'], [
            new BufferHandler(
                (new StreamHandler(
                    $monologConfig['path'],
                    $monologConfig['level']
                ))->setFormatter(new LogstashFormatter('app'))
            ),
        ]);
    }
}
