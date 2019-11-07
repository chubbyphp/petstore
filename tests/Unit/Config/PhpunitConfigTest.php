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
                    'charset' => 'utf8',
                    'dbname' => 'petstore_phpunit',
                    'driver' => 'pdo_pgsql',
                    'host' => 'postgres',
                    'port' => 5432,
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
