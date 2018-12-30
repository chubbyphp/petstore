<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\PhpunitConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\PhpunitConfig
 */
final class PhpunitConfigTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = PhpunitConfig::create('/path/to/root');

        self::assertSame([
            'config.cleanDirectories' => [
                'cache' => '/path/to/root/var/cache/phpunit',
                'log' => '/path/to/root/var/log/phpunit',
            ],
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'array',
                    ],
                ],
                'connection' => [
                    'charset' => 'utf8mb4',
                    'dbname' => 'petshop_phpunit',
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
                'proxies.dir' => '/path/to/root/var/cache/phpunit/doctrine/proxies',
            ],
        ], $config->getConfig());
    }

    public function testGetSlimSettings(): void
    {
        $config = PhpunitConfig::create('/path/to/root');

        self::assertSame([
            'displayErrorDetails' => true,
            'routerCacheFile' => false,
        ], $config->getSlimSettings());
    }

    public function testGetDirectories(): void
    {
        $config = PhpunitConfig::create('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/phpunit',
            'log' => '/path/to/root/var/log/phpunit',
        ], $config->getDirectories());
    }
}
