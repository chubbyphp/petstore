<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class PetCrudRequestHandlerTest extends AbstractIntegrationTest
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.16',
            'title' => 'Unsupported Media Type',
            'detail' => null,
            'instance' => null,
            'mediaType' => 'text/html',
            'supportedMediaTypes' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc4918#section-11.2',
            'title' => 'Unprocessable Entity',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'name',
                    'reason' => 'constraint.notblank.blank',
                    'details' => [],
                ],
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    /**
     * @depends testCreate
     */
    public function testListWithValidationError(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets?offset=test',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(400, $response['status']['code']);

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.1',
            'title' => 'Bad Request',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'offset',
                    'reason' => 'constraint.type.invalidtype',
                    'details' => [
                        'type' => 'string',
                        'wishedType' => 'integer',
                    ],
                ],
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testReadWithNotFound(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets/00000000-0000-0000-0000-000000000000',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(404, $response['status']['code']);

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.5',
            'title' => 'Not Found',
            'detail' => null,
            'instance' => null,
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.16',
            'title' => 'Unsupported Media Type',
            'detail' => null,
            'instance' => null,
            'mediaType' => 'text/html',
            'supportedMediaTypes' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.5',
            'title' => 'Not Found',
            'detail' => null,
            'instance' => null,
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc4918#section-11.2',
            'title' => 'Unprocessable Entity',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'name',
                    'reason' => 'constraint.notblank.blank',
                    'details' => [],
                ],
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.7',
            'title' => 'Not Acceptable',
            'detail' => null,
            'instance' => null,
            'accept' => 'text/html',
            'acceptables' => [
                'application/json',
                'application/x-jsonx',
                'application/x-www-form-urlencoded',
                'application/xml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
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

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true);

        self::assertEquals([
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10.4.5',
            'title' => 'Not Found',
            'detail' => null,
            'instance' => null,
            '_type' => 'apiProblem',
        ], $apiProblem);
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
