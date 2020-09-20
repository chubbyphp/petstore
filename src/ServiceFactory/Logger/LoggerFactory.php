<?php

declare(strict_types=1);

namespace App\ServiceFactory\Logger;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config = $container->get('config')['monolog'];

        return new Logger($config['name'], [
            new BufferHandler(
                (new StreamHandler(
                    $config['path'],
                    $config['level']
                ))->setFormatter(new LogstashFormatter('app'))
            ),
        ]);
    }
}
