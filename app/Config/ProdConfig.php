<?php

declare(strict_types=1);

namespace App\Config;

use Monolog\Logger;

class ProdConfig extends AbstractConfig
{
    public function getConfig(): array
    {
        $cacheDir = $this->getCacheDir();
        $logDir = $this->getLogDir();

        return [
            'cors' => [
                'allow-origin' => [],
                'allow-methods' => ['DELETE', 'GET', 'POST', 'PUT'],
                'allow-headers' => [
                    'Accept',
                    'Content-Type',
                ],
                'allow-credentials' => false,
                'expose-headers' => [],
                'max-age' => 7200,
            ],
            'debug' => false,
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => ['type' => 'apcu'],
                ],
                'connection' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => $_ENV['DATABASE_USER'],
                    'password' => $_ENV['DATABASE_PASS'],
                    'host' => $_ENV['DATABASE_HOST'],
                    'port' => $_ENV['DATABASE_PORT'],
                    'dbname' => $_ENV['DATABASE_NAME'],
                ],
            ],
            'doctrine.orm.em.options' => [
                'cache.hydration' => ['type' => 'apcu'],
                'cache.metadata' => ['type' => 'apcu'],
                'cache.query' => ['type' => 'apcu'],
                'proxies.dir' => $cacheDir.'/doctrine/proxies',
            ],
            'monolog' => [
                'name' => 'petstore',
                'path' => $logDir.'/application.log',
                'level' => Logger::NOTICE,
            ],
            'routerCacheFile' => $cacheDir.'/routes.php',
        ];
    }

    public function getEnv(): string
    {
        return 'prod';
    }
}
