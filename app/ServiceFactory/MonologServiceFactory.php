<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class MonologServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            LoggerInterface::class => static function (ContainerInterface $container) {
                $monolog = $container->get('monolog');

                return new Logger($monolog['name'], [
                    (new StreamHandler(
                        $monolog['path'],
                        $monolog['level']
                    ))->setFormatter(new LogstashFormatter('app')),
                ]);
            },
            'logger' => static function (ContainerInterface $container) {
                return $container->get(LoggerInterface::class);
            },
        ];
    }
}
