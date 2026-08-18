<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\ServiceFactory\Http;

use App\Core\ServiceFactory\Http\HttpClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Core\ServiceFactory\Http\HttpClientFactory
 *
 * @internal
 */
final class HttpClientFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $factory = new HttpClientFactory();

        $client = $factory();

        self::assertInstanceOf(Client::class, $client);

        self::assertSame(5, $client->getConfig(RequestOptions::TIMEOUT));
        self::assertSame(2, $client->getConfig(RequestOptions::CONNECT_TIMEOUT));
        self::assertFalse($client->getConfig(RequestOptions::ALLOW_REDIRECTS));
    }
}
