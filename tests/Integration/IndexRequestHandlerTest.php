<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 * @coversNothing
 */
final class IndexRequestHandlerTest extends AbstractIntegrationTest
{
    public function testIndex(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/'
        );

        self::assertSame(302, $response['status']['code']);

        self::assertStringEndsWith('/api/swagger/index', $response['headers']['location'][0]);
    }
}
