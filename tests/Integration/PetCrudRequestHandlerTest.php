<?php

declare(strict_types=1);

namespace App\Tests\Integration;

/**
 * @internal
 *
 * @coversNothing
 */
final class PetCrudRequestHandlerTest extends AbstractIntegrationTestCase
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

        self::assertSame(406, $response['status']['code'], $response['body'] ?? '');
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

        self::assertSame(415, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.16',
            'status' => 415,
            'title' => 'Unsupported Media Type',
            'detail' => 'Not supported content-type, supportedValues: "application/json", application/jsonx+xml", application/x-www-form-urlencoded", application/x-yaml"',
            'instance' => null,
            'value' => 'text/html',
            'supportedValues' => [
                'application/json',
                'application/jsonx+xml',
                'application/x-www-form-urlencoded',
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

        self::assertSame(422, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc4918#section-11.2',
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'name',
                    'reason' => 'Min length {{min}}, 0 given',
                    'details' => [
                        '_template' => 'Min length {{min}}, {{given}} given',
                        'minLength' => 1,
                        'given' => 0,
                    ],
                ],
            ],
            '_type' => 'apiProblem',
        ], $apiProblem);
    }

    public function testCreate(): void
    {
        $pet = $this->create();

        $this::assertPet(
            $pet,
            ['name' => 'Kathy', 'tag' => '134.456.789', 'vaccinations' => [['name' => 'Rabies']]],
            false
        );
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

        self::assertSame(406, $response['status']['code'], $response['body'] ?? '');
    }

    public function testListWithValidationError(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/pets?filters[name2]=test&sort[name]=test',
            [
                'Accept' => 'application/json',
            ]
        );

        self::assertSame(400, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.1',
            'status' => 400,
            'title' => 'Bad Request',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'filters[name2]',
                    'reason' => 'Unknown field "name2"',
                    'details' => [
                        '_template' => 'Unknown field {{fieldName}}',
                        'fieldName' => 'name2',
                    ],
                ],
                [
                    'name' => 'sort[name]',
                    'reason' => 'Input should be "asc", "test" given',
                    'details' => [
                        '_template' => 'Input should be {{expected}}, {{given}} given',
                        'expected' => 'asc',
                        'given' => 'test',
                    ],
                ],
                [
                    'name' => 'sort[name]',
                    'reason' => 'Input should be "desc", "test" given',
                    'details' => [
                        '_template' => 'Input should be {{expected}}, {{given}} given',
                        'expected' => 'desc',
                        'given' => 'test',
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

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $petCollection = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('offset', $petCollection);
        self::assertArrayHasKey('limit', $petCollection);
        self::assertArrayHasKey('filters', $petCollection);
        self::assertArrayHasKey('sort', $petCollection);
        self::assertArrayHasKey('count', $petCollection);
        self::assertArrayHasKey('items', $petCollection);
        self::assertArrayHasKey('_links', $petCollection);
        self::assertArrayHasKey('list', $petCollection['_links']);
        self::assertArrayHasKey('create', $petCollection['_links']);
        self::assertArrayHasKey('_type', $petCollection);

        self::assertSame(0, $petCollection['offset']);
        self::assertSame(20, $petCollection['limit']);
        self::assertSame(['name' => 'desc'], $petCollection['sort']);
        self::assertGreaterThanOrEqual(1, $petCollection['count']);
        self::assertGreaterThanOrEqual(1, is_countable($petCollection['items']) ? \count($petCollection['items']) : 0);
        self::assertSame($petCollection['count'], is_countable($petCollection['items']) ? \count($petCollection['items']) : 0);

        $found = false;

        foreach ($petCollection['items'] as $item) {
            if ($item['id'] === $pet['id']) {
                $this::assertPet(
                    $item,
                    ['name' => 'Kathy', 'tag' => '134.456.789', 'vaccinations' => [['name' => 'Rabies']]],
                    false
                );
                $found = true;
            }
        }

        self::assertTrue($found);

        self::assertSame(['href' => '/api/pets?offset=0&limit=20&sort%5Bname%5D=desc',
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

        self::assertSame(406, $response['status']['code'], $response['body'] ?? '');
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

        self::assertSame(404, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.5',
            'status' => 404,
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

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        $this::assertPet(
            $pet,
            ['name' => 'Kathy', 'tag' => '134.456.789', 'vaccinations' => [['name' => 'Rabies']]],
            false
        );
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

        self::assertSame(406, $response['status']['code'], $response['body'] ?? '');
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

        self::assertSame(415, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.16',
            'status' => 415,
            'title' => 'Unsupported Media Type',
            'detail' => 'Not supported content-type, supportedValues: "application/json", application/jsonx+xml", application/x-www-form-urlencoded", application/x-yaml"',
            'instance' => null,
            'value' => 'text/html',
            'supportedValues' => [
                'application/json',
                'application/jsonx+xml',
                'application/x-www-form-urlencoded',
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

        self::assertSame(404, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.5',
            'status' => 404,
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

        self::assertSame(422, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc4918#section-11.2',
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => null,
            'instance' => null,
            'invalidParameters' => [
                [
                    'name' => 'name',
                    'reason' => 'Min length {{min}}, 0 given',
                    'details' => [
                        '_template' => 'Min length {{min}}, {{given}} given',
                        'minLength' => 1,
                        'given' => 0,
                    ],
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
            json_encode($existingPet, JSON_THROW_ON_ERROR)
        );

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        $this::assertPet(
            $pet,
            ['name' => 'Kathy', 'tag' => '134.456.789', 'vaccinations' => [['name' => 'Rabies']]],
            true
        );
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

        self::assertSame(200, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        $this::assertPet($pet, ['name' => 'Momo', 'tag' => null, 'vaccinations' => []], true);
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

        self::assertSame(406, $response['status']['code'], $response['body'] ?? '');
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

        self::assertSame(404, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/problem+json', $response['headers']['content-type'][0]);

        $apiProblem = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.5',
            'status' => 404,
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

        self::assertSame(204, $response['status']['code'], $response['body'] ?? '');
    }

    private function create(): array
    {
        $response = $this->httpRequest(
            'POST',
            '/api/pets',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            json_encode(['name' => 'Kathy', 'tag' => '134.456.789', 'vaccinations' => [['name' => 'Rabies']]])
        );

        self::assertSame(201, $response['status']['code'], $response['body'] ?? '');

        self::assertSame('application/json', $response['headers']['content-type'][0]);

        $pet = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($pet);

        return $pet;
    }

    private static function assertPet(array $pet, array $expectedPet, bool $updated): void
    {
        self::assertArrayHasKey('id', $pet);
        self::assertArrayHasKey('createdAt', $pet);
        self::assertArrayHasKey('updatedAt', $pet);
        self::assertArrayHasKey('name', $pet);
        self::assertArrayHasKey('tag', $pet);
        self::assertArrayHasKey('vaccinations', $pet);
        self::assertArrayHasKey('_links', $pet);
        self::assertArrayHasKey('read', $pet['_links']);
        self::assertArrayHasKey('update', $pet['_links']);
        self::assertArrayHasKey('delete', $pet['_links']);
        self::assertArrayHasKey('_type', $pet);

        self::assertMatchesRegularExpression(self::UUID_PATTERN, $pet['id']);
        self::assertMatchesRegularExpression(self::DATE_PATTERN, $pet['createdAt']);

        if ($updated) {
            self::assertMatchesRegularExpression(self::DATE_PATTERN, $pet['updatedAt']);
        } else {
            self::assertNull($pet['updatedAt']);
        }

        self::assertSame($expectedPet['name'], $pet['name']);
        self::assertSame($expectedPet['tag'], $pet['tag']);

        foreach ($expectedPet['vaccinations'] as $i => $expectedVaccination) {
            self::assertVaccination($pet['vaccinations'][$i], $expectedVaccination);
        }
        self::assertSame(is_countable($expectedPet['vaccinations']) ? \count($expectedPet['vaccinations']) : 0, is_countable($pet['vaccinations']) ? \count($pet['vaccinations']) : 0);
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

    private static function assertVaccination(array $vaccination, array $expectedVaccination): void
    {
        self::assertArrayHasKey('name', $vaccination);

        self::assertSame($expectedVaccination['name'], $vaccination['name']);
        self::assertSame('vaccination', $vaccination['_type']);
    }
}
