<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 * @coversNothing
 */
final class CorsControllerTest extends AbstractIntegrationTest
{
    public function testCorsHeaderWithMatchingOrigin(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/ping',
            [
                'Accept' => 'application/json',
                'Origin' => 'http://localhost:3000',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('http://localhost:3000', $response['headers']['access-control-allow-origin'][0]);
        self::assertSame('false', $response['headers']['access-control-allow-credentials'][0]);
        self::assertArrayNotHasKey('access-control-expose-headers', $response['headers']);
    }

    public function testCorsHeaderWithNotMatchingOrigin(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/ping',
            [
                'Accept' => 'application/json',
                'Origin' => 'https://unknown.local',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertArrayNotHasKey('access-control-allow-origin', $response['headers']);
        self::assertArrayNotHasKey('access-control-allow-credentials', $response['headers']);
        self::assertArrayNotHasKey('access-control-expose-headers', $response['headers']);
    }

    public function testCorsPreflightHeaderWithMatchingOrigin(): void
    {
        $response = $this->httpRequest(
            'OPTIONS',
            '/api/ping',
            [
                'Accept' => 'application/json',
                'Origin' => 'https://localhost:3000',
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'Accept, Content-Type',
            ]
        );

        self::assertSame(204, $response['status']['code']);

        self::assertSame('https://localhost:3000', $response['headers']['access-control-allow-origin'][0]);
        self::assertSame('false', $response['headers']['access-control-allow-credentials'][0]);
        self::assertArrayNotHasKey('access-control-expose-headers', $response['headers']);
        self::assertSame('DELETE, GET, POST, PUT', $response['headers']['access-control-allow-methods'][0]);
        self::assertSame('Accept, Content-Type', $response['headers']['access-control-allow-headers'][0]);

        self::assertSame('7200', $response['headers']['access-control-max-age'][0]);
    }

    public function testCorsPreflightHeaderWithNotMatchingOrigin(): void
    {
        $response = $this->httpRequest(
            'OPTIONS',
            '/api/ping',
            [
                'Accept' => 'application/json',
                'Origin' => 'https://unknown.local',
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'Accept, Content-Type',
            ]
        );

        self::assertSame(204, $response['status']['code']);

        self::assertArrayNotHasKey('access-control-allow-origin', $response['headers']);
        self::assertArrayNotHasKey('access-control-allow-credentials', $response['headers']);
        self::assertArrayNotHasKey('access-control-expose-headers', $response['headers']);
        self::assertArrayNotHasKey('access-control-allow-method', $response['headers']);
        self::assertArrayNotHasKey('access-control-allow-headers', $response['headers']);
        self::assertArrayNotHasKey('access-control-max-age', $response['headers']);
    }

    public function testCorsOnAUnknownRoute(): void
    {
        $response = $this->httpRequest(
            'OPTIONS',
            '/some/route',
            [
                'Accept' => 'application/json',
                'Origin' => 'https://unknown.local',
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'Accept, Content-Type',
            ]
        );

        // without asking the router this is not preventable with a middleware only design
        self::assertSame(204, $response['status']['code']);
    }
}
