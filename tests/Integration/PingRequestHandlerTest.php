<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 * @coversNothing
 */
final class PingRequestHandlerTest extends AbstractIntegrationTest
{
    public function testPingWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/ping',
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = \json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/jsonx+xml',
                'application/x-www-form-urlencoded',
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testPing(): void
    {
        $now = \DateTime::createFromFormat(\DateTime::ATOM, \date('c'));

        $response = $this->httpRequest(
            'GET',
            '/api/ping',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);
        self::assertSame('no-cache, no-store, must-revalidate', $response['headers']['cache-control'][0]);
        self::assertSame('0', $response['headers']['expires'][0]);
        self::assertSame('no-cache', $response['headers']['pragma'][0]);

        $ping = \json_decode($response['body'], true);

        self::assertArrayHasKey('date', $ping);

        $date = new \DateTime($ping['date']);

        self::assertGreaterThanOrEqual($now, $date);
    }
}
