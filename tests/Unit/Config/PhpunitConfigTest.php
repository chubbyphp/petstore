<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use App\Config\PhpunitConfig;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\AbstractConfig
 * @covers \App\Config\PhpunitConfig
 *
 * @internal
 */
final class PhpunitConfigTest extends TestCase
{
    public function testGetConfig(): void
    {
        $_ENV['DATABASE_USER'] = 'database_phpunit_user';
        $_ENV['DATABASE_PASS'] = 'database_phpunit_pass';
        $_ENV['DATABASE_HOST'] = 'database_phpunit_host';
        $_ENV['DATABASE_PORT'] = 'database_phpunit_port';
        $_ENV['DATABASE_NAME'] = 'database_phpunit_name';

        $config = new PhpunitConfig('/path/to/root');

        self::assertSame([
            'cors' => [
                'allow-origin' => [
                    '^https?://localhost:3000' => AllowOriginRegex::class,
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
                    'user' => 'database_phpunit_user',
                    'password' => 'database_phpunit_pass',
                    'host' => 'database_phpunit_host',
                    'port' => 'database_phpunit_port',
                    'dbname' => 'database_phpunit_name_phpunit',
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
            'monolog' => [
                'name' => 'petstore',
                'path' => '/path/to/root/var/log/phpunit/application.log',
                'level' => 100,
            ],
            'routerCacheFile' => null,
        ], $config->getConfig());
    }

    public function testGetDirectories(): void
    {
        $config = new PhpunitConfig('/path/to/root');

        self::assertSame([
            'cache' => '/path/to/root/var/cache/phpunit',
            'log' => '/path/to/root/var/log/phpunit',
        ], $config->getDirectories());
    }

    public function testGetEnvironment(): void
    {
        $config = new PhpunitConfig('/path/to/root');

        self::assertSame('phpunit', $config->getEnv());
    }
}
