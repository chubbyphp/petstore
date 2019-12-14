<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\ProdConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\ProdConfig
 *
 * @internal
 */
final class ProdConfigTest extends TestCase
{
    public function testGetConfig(): void
    {
        $_ENV['DATABASE_USER'] = 'database_prod_user';
        $_ENV['DATABASE_PASS'] = 'database_prod_pass';
        $_ENV['DATABASE_HOST'] = 'database_prod_host';
        $_ENV['DATABASE_PORT'] = 'database_prod_port';
        $_ENV['DATABASE_NAME'] = 'database_prod_name';

        $config = new ProdConfig('/path/to/root');

        self::assertSame([
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
                    'cache.result' => [
                        'type' => 'apcu',
                    ],
                ],
                'connection' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => 'database_prod_user',
                    'password' => 'database_prod_pass',
                    'host' => 'database_prod_host',
                    'port' => 'database_prod_port',
                    'dbname' => 'database_prod_name',
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
            'monolog' => [
                'name' => 'petstore',
                'path' => '/path/to/root/var/log/prod/application.log',
                'level' => 250,
            ],
            'routerCacheFile' => '/path/to/root/var/cache/prod/routes.php',
        ], $config->getConfig());
    }

    public function testGetDirectories(): void
    {
        $config = new ProdConfig('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/prod',
            'log' => '/path/to/root/var/log/prod',
        ], $config->getDirectories());
    }

    public function testGetEnvironment(): void
    {
        $config = new ProdConfig('/path/to/root');

        self::assertSame('prod', $config->getEnv());
    }
}
