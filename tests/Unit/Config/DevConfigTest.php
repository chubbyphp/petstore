<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\DevConfig;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\DevConfig
 *
 * @internal
 */
final class DevConfigTest extends TestCase
{
    public function testGetConfig(): void
    {
        putenv('DATABASE_USER=database_dev_user');
        putenv('DATABASE_PASS=database_dev_pass');
        putenv('DATABASE_HOST=database_dev_host');
        putenv('DATABASE_PORT=database_dev_port');
        putenv('DATABASE_NAME=database_dev_name');

        $config = new DevConfig('/path/to/root');

        self::assertSame([
            'cors' => [
                'allow-origin' => [
                    '^https?://localhost' => AllowOriginRegex::class,
                ],
                'allow-methods' => ['DELETE', 'GET', 'POST', 'PUT'],
                'allow-headers' => [
                    'Accept',
                    'Content-Type',
                ],
                'allow-credentials' => false,
                'expose-headers' => [],
                'max-age' => 7200,
            ],
            'debug' => true,
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => [
                        'type' => 'array',
                    ],
                ],
                'connection' => [
                    'driver' => 'pdo_pgsql',
                    'charset' => 'utf8',
                    'user' => 'database_dev_user',
                    'password' => 'database_dev_pass',
                    'host' => 'database_dev_host',
                    'port' => 'database_dev_port',
                    'dbname' => 'database_dev_name',
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
                'proxies.dir' => '/path/to/root/var/cache/dev/doctrine/proxies',
            ],
            'monolog' => [
                'name' => 'petstore',
                'path' => '/path/to/root/var/log/dev/application.log',
                'level' => 100,
            ],
            'routerCacheFile' => null,
        ], $config->getConfig());
    }

    public function testGetDirectories(): void
    {
        $config = new DevConfig('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/dev',
            'log' => '/path/to/root/var/log/dev',
        ], $config->getDirectories());
    }

    public function testGetEnvironment(): void
    {
        $config = new DevConfig('/path/to/root');

        self::assertSame('dev', $config->getEnv());
    }
}
