<?php

declare(strict_types=1);

namespace App\Tests\Integration\Pet;

use App\Tests\Integration\AbstractIntegrationTest;

final class CreateControllerTest extends AbstractIntegrationTest
{
    public function testSuccessful()
    {
        $response = $this->httpRequest(
            'POST',
            '/pets',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode(['name' => 'Frida'])
        );

        self::assertSame(201, $response['status']['code']);
        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true);

        self::assertArrayHasKey('id', $pet);
        self::assertArrayHasKey('createdAt', $pet);
        self::assertArrayHasKey('updatedAt', $pet);
        self::assertArrayHasKey('name', $pet);
        self::assertArrayHasKey('tag', $pet);
        self::assertArrayHasKey('_type', $pet);

        self::assertRegExp(self::UUID_PATTERN, $pet['id']);
        // todo: regex for createdAT
        self::assertNull($pet['updatedAt']);
        self::assertSame('Frida', $pet['name']);
        self::assertNull($pet['tag']);
        self::assertSame('pet', $pet['_type']);
    }
}
