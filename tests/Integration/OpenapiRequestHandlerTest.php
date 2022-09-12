<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class OpenapiRequestHandlerTest extends AbstractIntegrationTest
{
    public function testOpenapi(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/openapi'
        );

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertStringStartsWith('application/x-yaml', $response['headers']['content-type'][0]);

        self::assertStringContainsString('title: Petstore', $response['body']);
    }
}
