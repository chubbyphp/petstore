<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class PetCrudControllerTest extends AbstractIntegrationTest
{
    public function testCreateWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'POST',
            '/api/pets',
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame(
            'Accept "text/html" is not supported, supported are "application/json", "application/x-jsonx"'
                . ', "application/x-www-form-urlencoded", "application/xml"',
            $response['headers']['x-not-acceptable'][0]
        );

        self::assertNull($response['body']);
    }

    public function testCreateWithUnsupportedContentType(): void
    {
        $response = $this->httpRequest(
            'POST',
            '/api/pets',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'text/html',
            ]
        );

        self::assertSame(415, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'header',
            'key' => 'contentype_not_supported',
            'detail' => 'the given content type is not supported',
            'reference' => null,
            'arguments' => [
                'contentType' => 'text/html',
                'supportedContentTypes' => [
                    'application/json',
                    'application/x-jsonx',
                    'application/x-www-form-urlencoded',
                    'application/xml',
                ],
            ],
            '_type' => 'error',
        ], $error);
    }

    public function testCreateWithValidationError(): void
    {
        $response = $this->httpRequest(
            'POST',
            '/api/pets',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode(['name' => ''])
        );

        self::assertSame(422, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'body',
            'key' => 'validation',
            'detail' => 'there are validation errors',
            'reference' => null,
            'arguments' => [
                'name' => [
                    [
                            'key' => 'constraint.notblank.blank',
                            'arguments' => [
                        ],
                    ],
                ],
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @return array
     */
    public function testCreate(): array
    {
        $response = $this->httpRequest(
            'POST',
            '/api/pets',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode(['name' => 'Kathy', 'tag' => '134.456.789'])
        );

        self::assertSame(201, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true);

        $this::assertPet($pet, ['name' => 'Kathy', 'tag' => '134.456.789'], false);

        return $pet;
    }

    /**
     * @depends testCreate
     */
    public function testListWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets',
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame(
            'Accept "text/html" is not supported, supported are "application/json", "application/x-jsonx"'
                . ', "application/x-www-form-urlencoded", "application/xml"',
            $response['headers']['x-not-acceptable'][0]
        );

        self::assertNull($response['body']);
    }

    /**
     * @depends testCreate
     */
    public function testList(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $petCollection = json_decode($response['body'], true);

        self::assertArrayHasKey('offset', $petCollection);
        self::assertArrayHasKey('limit', $petCollection);
        self::assertArrayHasKey('_embedded', $petCollection);
        self::assertArrayHasKey('items', $petCollection['_embedded']);
        self::assertArrayHasKey('_links', $petCollection);
        self::assertArrayHasKey('list', $petCollection['_links']);
        self::assertArrayHasKey('create', $petCollection['_links']);
        self::assertArrayHasKey('_type', $petCollection);

        self::assertSame(0, $petCollection['offset']);
        self::assertSame(20, $petCollection['limit']);

        $this::assertPet($petCollection['_embedded']['items'][0], ['name' => 'Kathy', 'tag' => '134.456.789'], false);

        self::assertCount(1, $petCollection['_embedded']['items']);
        self::assertSame([
            'href' => '/api/pets?offset=0&limit=20',
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $petCollection['_links']['list']);
        self::assertSame([
            'href' => '/api/pets',
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'POST',
            ],
        ], $petCollection['_links']['create']);
        self::assertSame('petCollection', $petCollection['_type']);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testReadWithUnsupportedAccept(array $existingPet): void
    {
        $response = $this->httpRequest(
            'GET',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame(
            'Accept "text/html" is not supported, supported are "application/json", "application/x-jsonx"'
                . ', "application/x-www-form-urlencoded", "application/xml"',
            $response['headers']['x-not-acceptable'][0]
        );

        self::assertNull($response['body']);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testReadWithNotFound(array $existingPet): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets/00000000-0000-0000-0000-000000000000',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(404, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'resource',
            'key' => 'resource_not_found',
            'detail' => 'the requested resource cannot be found',
            'reference' => null,
            'arguments' => [
                'model' => '00000000-0000-0000-0000-000000000000',
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testRead(array $existingPet): void
    {
        $response = $this->httpRequest(
            'GET',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true);

        $this::assertPet($pet, ['name' => 'Kathy', 'tag' => '134.456.789'], false);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdateWithUnsupportedAccept(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame(
            'Accept "text/html" is not supported, supported are "application/json", "application/x-jsonx"'
                . ', "application/x-www-form-urlencoded", "application/xml"',
            $response['headers']['x-not-acceptable'][0]
        );

        self::assertNull($response['body']);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdateWithUnsupportedContentType(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
                'Content-Type' => 'text/html',
            ]
        );

        self::assertSame(415, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'header',
            'key' => 'contentype_not_supported',
            'detail' => 'the given content type is not supported',
            'reference' => null,
            'arguments' => [
                'contentType' => 'text/html',
                'supportedContentTypes' => [
                    'application/json',
                    'application/x-jsonx',
                    'application/x-www-form-urlencoded',
                    'application/xml',
                ],
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdateWithNotFound(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            '/api/pets/00000000-0000-0000-0000-000000000000',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        );

        self::assertSame(404, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'resource',
            'key' => 'resource_not_found',
            'detail' => 'the requested resource cannot be found',
            'reference' => null,
            'arguments' => [
                'model' => '00000000-0000-0000-0000-000000000000',
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdateWithValidationError(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode(['name' => ''])
        );

        self::assertSame(422, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'body',
            'key' => 'validation',
            'detail' => 'there are validation errors',
            'reference' => null,
            'arguments' => [
                'name' => [
                    [
                            'key' => 'constraint.notblank.blank',
                            'arguments' => [
                        ],
                    ],
                ],
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdateByCreateData(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode($existingPet)
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true);

        $this::assertPet($pet, ['name' => 'Kathy', 'tag' => '134.456.789'], true);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testUpdate(array $existingPet): void
    {
        $response = $this->httpRequest(
            'PUT',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode([
                'name' => 'Momo',
            ])
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true);

        $this::assertPet($pet, ['name' => 'Momo', 'tag' => null], true);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testDeleteWithUnsupportedAccept(array $existingPet): void
    {
        $response = $this->httpRequest(
            'DELETE',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'text/html',
            ]
        );

        self::assertSame(406, $response['status']['code']);

        self::assertSame(
            'Accept "text/html" is not supported, supported are "application/json", "application/x-jsonx"'
                . ', "application/x-www-form-urlencoded", "application/xml"',
            $response['headers']['x-not-acceptable'][0]
        );

        self::assertNull($response['body']);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testDeleteWithNotFound(array $existingPet): void
    {
        $response = $this->httpRequest(
            'DELETE',
            '/api/pets/00000000-0000-0000-0000-000000000000',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(404, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $error = json_decode($response['body'], true);

        self::assertEquals([
            'scope' => 'resource',
            'key' => 'resource_not_found',
            'detail' => 'the requested resource cannot be found',
            'reference' => null,
            'arguments' => [
                'model' => '00000000-0000-0000-0000-000000000000',
            ],
            '_type' => 'error',
        ], $error);
    }

    /**
     * @depends testCreate
     *
     * @param array $existingPet
     */
    public function testDelete(array $existingPet): void
    {
        $response = $this->httpRequest(
            'DELETE',
            sprintf('/api/pets/%s', $existingPet['id']),
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(204, $response['status']['code']);
    }

    /**
     * @param array $pet
     * @param array $expectedValues
     * @param bool  $updated
     */
    private static function assertPet(array $pet, array $expectedValues, bool $updated): void
    {
        self::assertArrayHasKey('id', $pet);
        self::assertArrayHasKey('createdAt', $pet);
        self::assertArrayHasKey('updatedAt', $pet);
        self::assertArrayHasKey('name', $pet);
        self::assertArrayHasKey('tag', $pet);
        self::assertArrayHasKey('_links', $pet);
        self::assertArrayHasKey('read', $pet['_links']);
        self::assertArrayHasKey('update', $pet['_links']);
        self::assertArrayHasKey('delete', $pet['_links']);
        self::assertArrayHasKey('_type', $pet);

        self::assertRegExp(self::UUID_PATTERN, $pet['id']);
        self::assertRegExp(self::DATE_PATTERN, $pet['createdAt']);

        if ($updated) {
            self::assertRegExp(self::DATE_PATTERN, $pet['updatedAt']);
        } else {
            self::assertNull($pet['updatedAt']);
        }

        self::assertSame($expectedValues['name'], $pet['name']);
        self::assertSame($expectedValues['tag'], $pet['tag']);
        self::assertSame([
            'href' => sprintf('/api/pets/%s', $pet['id']),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $pet['_links']['read']);
        self::assertSame([
            'href' => sprintf('/api/pets/%s', $pet['id']),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'PUT',
            ],
        ], $pet['_links']['update']);
        self::assertSame([
            'href' => sprintf('/api/pets/%s', $pet['id']),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'DELETE',
            ],
        ], $pet['_links']['delete']);
        self::assertSame('pet', $pet['_type']);
    }
}
