<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\CiConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\CiConfig
 */
final class CiConfigTest extends TestCase
{
    public function testGetConfig()
    {
        $config = CiConfig::create('/path/to/root');

        self::assertSame([
            'config.cleanDirectories' => [
                'cache' => '/path/to/root/var/cache/ci',
                'log' => '/path/to/root/var/log/ci',
            ],
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'array',
                    ],
                ],
                'connection' => [
                    'charset' => 'utf8mb4',
                    'dbname' => 'petshop_ci',
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'password' => 'root',
                    'user' => 'root',
                ],
            ],
            'doctrine.orm.em.options' => [
                'cache.hydration' => [
                    'type' => 'array',
                ],
                'cache.metadata' => [
                    'type' => 'array',
                ],
                'cache.query' => [
                    'type' => 'array',
                ],
                'proxies.dir' => '/path/to/root/var/cache/ci/doctrine/proxies',
            ],
        ], $config->getConfig());
    }

    public function testGetSlimSettings()
    {
        $config = CiConfig::create('/path/to/root');

        self::assertSame([
            'displayErrorDetails' => true,
            'routerCacheFile' => false,
        ], $config->getSlimSettings());
    }

    public function testGetDirectories()
    {
        $config = CiConfig::create('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/ci',
            'log' => '/path/to/root/var/log/ci',
        ], $config->getDirectories());
    }
}
