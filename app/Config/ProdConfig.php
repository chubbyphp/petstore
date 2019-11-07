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
                    'charset' => 'utf8',
                    'dbname' => 'petstore',
                    'driver' => 'pdo_pgsql',
                    'host' => 'postgres',
                    'port' => 5432,
                    'password' => 'root',
                    'user' => 'root',
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
