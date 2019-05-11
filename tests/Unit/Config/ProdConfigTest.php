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
    public function testGetConfig(): void
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'apcu',
                    ],
                ],
                'connection' => [
                    'charset' => 'utf8',
                    'dbname' => 'petshop',
                    'driver' => 'pdo_pgsql',
                    'host' => 'localhost',
                    'port' => 5432,
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

    public function testGetSlimSettings(): void
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'displayErrorDetails' => false,
            'routerCacheFile' => '/path/to/root/var/cache/prod/routes.php',
        ], $config->getSlimSettings());
    }

    public function testGetDirectories(): void
    {
        $config = ProdConfig::create('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/prod',
            'log' => '/path/to/root/var/log/prod',
        ], $config->getDirectories());
    }
}
