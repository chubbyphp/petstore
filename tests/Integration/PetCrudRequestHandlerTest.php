<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 * @coversNothing
 */
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
                'application/x-yaml',
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
                'application/x-yaml',
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

    public function testCreate(): void
    {
        $pet = $this->create();

        $this::assertPet($pet, ['name' => 'Kathy', 'tag' => '134.456.789'], false);
    }

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
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testListWithValidationError(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets?offset=test&filters[name2]=test&sort[name]=test',
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
                [
                    'name' => 'filters[name2]',
                    'reason' => 'constraint.map.field.notallowed',
                    'details' => [
                        'field' => 'name2',
                        'allowedFields' => ['name'],
                    ],
                ],
                [
                    'name' => 'sort',
                    'reason' => 'constraint.sort.order.notallowed',
                    'details' => [
                        'field' => 'name',
                        'order' => 'test',
                        'allowedOrders' => ['asc', 'desc'],
                    ],
                ],
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testList(): void
    {
        $pet = $this->create();

        $response = $this->httpRequest(
            'GET',
            '/api/pets?sort[name]=desc',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(200, $response['status']['code']);

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $petCollection = json_decode($response['body'], true);

        self::assertArrayHasKey('offset', $petCollection);
        self::assertArrayHasKey('limit', $petCollection);
        self::assertArrayHasKey('filters', $petCollection);
        self::assertArrayHasKey('sort', $petCollection);
        self::assertArrayHasKey('count', $petCollection);
        self::assertArrayHasKey('_embedded', $petCollection);
        self::assertArrayHasKey('items', $petCollection['_embedded']);
        self::assertArrayHasKey('_links', $petCollection);
        self::assertArrayHasKey('list', $petCollection['_links']);
        self::assertArrayHasKey('create', $petCollection['_links']);
        self::assertArrayHasKey('_type', $petCollection);

        self::assertSame(0, $petCollection['offset']);
        self::assertSame(20, $petCollection['limit']);
        self::assertSame(['name' => 'desc'], $petCollection['sort']);
        self::assertGreaterThanOrEqual(1, $petCollection['count']);
        self::assertGreaterThanOrEqual(1, count($petCollection['_embedded']['items']));
        self::assertSame($petCollection['count'], count($petCollection['_embedded']['items']));

        $found = false;
        foreach ($petCollection['_embedded']['items'] as $item) {
            if ($item['id'] === $pet['id']) {
                $this::assertPet($item, ['name' => 'Kathy', 'tag' => '134.456.789'], false);
                $found = true;
            }
        }

        self::assertTrue($found);

        self::assertSame([
            'href' => '/api/pets?sort%5Bname%5D=desc&offset=0&limit=20',
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

    public function testReadWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testReadWithNotFound(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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

    public function testRead(): void
    {
        $existingPet = $this->create();

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

    public function testUpdateWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'PUT',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testUpdateWithUnsupportedContentType(): void
    {
        $response = $this->httpRequest(
            'PUT',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testUpdateWithNotFound(): void
    {
        $response = $this->httpRequest(
            'PUT',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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

    public function testUpdateWithValidationError(): void
    {
        $existingPet = $this->create();

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

    public function testUpdateByCreateData(): void
    {
        $existingPet = $this->create();

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

    public function testUpdate(): void
    {
        $existingPet = $this->create();

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

    public function testDeleteWithUnsupportedAccept(): void
    {
        $response = $this->httpRequest(
            'DELETE',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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
                'application/x-yaml',
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testDeleteWithNotFound(): void
    {
        $response = $this->httpRequest(
            'DELETE',
            '/api/pets/e19a00b4-241e-4241-a641-bac2a4a65f64',
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

    public function testDelete(): void
    {
        $existingPet = $this->create();

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
     * @return array
     */
    private function create(): array
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

        self::assertIsArray($pet);

        return $pet;
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
