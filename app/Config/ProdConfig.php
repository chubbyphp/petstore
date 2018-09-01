<?php

declare(strict_types=1);

namespace App\Config;

class ProdConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'config.cleanDirectories' => $this->getDirectories(),
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => ['type' => 'apcu'],
                ],
                'connection' => [
                    'charset' => 'utf8mb4',
                    'dbname' => 'petshop',
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'password' => 'root',
                    'user' => 'root',
                ],
            ],
            'doctrine.orm.em.options' => [
                'cache.hydration' => ['type' => 'apcu'],
                'cache.metadata' => ['type' => 'apcu'],
                'cache.query' => ['type' => 'apcu'],
                'proxies.dir' => $this->getCacheDir().'/doctrine/proxies',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getSlimSettings(): array
    {
        return [
            'displayErrorDetails' => false,
            'routerCacheFile' => $this->getCacheDir().'/routes.php',
        ];
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'prod';
    }
}
