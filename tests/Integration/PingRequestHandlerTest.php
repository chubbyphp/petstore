<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class PingRequestHandlerTest extends AbstractIntegrationTest
{
    public function testPing(): void
    {
        $now = \DateTimeImmutable::createFromFormat(\DateTime::ATOM, date('c'));

        $response = $this->httpRequest(
            'GET',
            '/ping',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);
        self::assertSame('no-cache, no-store, must-revalidate', $response['headers']['cache-control'][0]);
        self::assertSame('0', $response['headers']['expires'][0]);
        self::assertSame('no-cache', $response['headers']['pragma'][0]);

        $ping = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('date', $ping);

        $date = new \DateTimeImmutable($ping['date']);

        self::assertGreaterThanOrEqual($now, $date);
    }
}
