<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
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

                return new Logger(
                    $monolog['name'],
                    [
                        new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $monolog['level'])
                    ],
                    [
                        new UidProcessor(),
                    ]
                );
            },
            'logger' => static function (ContainerInterface $container) {
                return $container->get(LoggerInterface::class);
            },
        ];
    }
}
