<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\ProdConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\ProdConfig
 */
final class ProdConfigTest extends TestCase
{
    public function testGetConfig()
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'config.cleanDirectories' => [
                'cache' => '/path/to/root/var/cache/prod',
                'log' => '/path/to/root/var/log/prod',
            ],
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'apcu',
                    ],
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
                'cache.hydration' => [
                    'type' => 'apcu',
                ],
                'cache.metadata' => [
                    'type' => 'apcu',
                ],
                'cache.query' => [
                    'type' => 'apcu',
                ],
                'proxies.dir' => '/path/to/root/var/cache/prod/doctrine/proxies',
            ],
        ], $config->getConfig());
    }

    public function testGetSlimSettings()
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'displayErrorDetails' => false,
            'routerCacheFile' => '/path/to/root/var/cache/prod/routes.php',
        ], $config->getSlimSettings());
    }

    public function testGetDirectories()
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/prod',
            'log' => '/path/to/root/var/log/prod',
        ], $config->getDirectories());
    }
}
