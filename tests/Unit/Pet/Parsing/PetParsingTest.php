<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\Parsing;

use App\Pet\Dto\Collection\PetCollectionRequest;
use App\Pet\Dto\Collection\PetCollectionResponse;
use App\Pet\Dto\Model\PetRequest;
use App\Pet\Dto\Model\PetResponse;
use App\Pet\Parsing\PetParsing;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Parsing\ErrorsException;
use Chubbyphp\Parsing\Parser;
use Mezzio\Helper\UrlHelperInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Pet\Parsing\PetParsing
 *
 * @internal
 */
final class PetParsingTest extends TestCase
{
    public function testGetCollectionRequestSchema(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $parser = new Parser();

        /** @var UrlHelperInterface $urlHelper */
        $urlHelper = $builder->create(UrlHelperInterface::class, []);

        $petParsing = new PetParsing($parser, $urlHelper);

        $petCollectionMinimalRequest = $petParsing->getCollectionRequestSchema($request)->parse([]);

        self::assertInstanceOf(PetCollectionRequest::class, $petCollectionMinimalRequest);

        self::assertSame([
            'offset' => 0,
            'limit' => 20,
            'filters' => ['name' => null],
            'sort' => ['name' => null],
        ], json_decode(json_encode($petCollectionMinimalRequest), true));

        $petCollectionMaximalRequest = $petParsing->getCollectionRequestSchema($request)->parse([
            'offset' => '10',
            'limit' => '10',
            'filters' => ['name' => 'jerry'],
            'sort' => ['name' => 'asc'],
        ]);

        self::assertInstanceOf(PetCollectionRequest::class, $petCollectionMaximalRequest);

        self::assertSame([
            'offset' => 10,
            'limit' => 10,
            'filters' => [
                'name' => 'jerry',
            ],
            'sort' => [
                'name' => 'asc',
            ],
        ], json_decode(json_encode($petCollectionMaximalRequest), true));
    }

    public function testGetCollectionResponseSchema(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $parser = new Parser();

        /** @var UrlHelperInterface $urlHelper */
        $urlHelper = $builder->create(UrlHelperInterface::class, [
            new WithReturn(
                'generate',
                ['pet_read', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_update', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_delete', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_list', [], ['offset' => 10, 'limit' => 10, 'filters' => ['name' => null], 'sort' => ['name' => null]], null, []],
                '/api/pets?offset=10&limit=10'
            ),
            new WithReturn(
                'generate',
                ['pet_create', [], [], null, []],
                '/api/pets'
            ),
            new WithReturn(
                'generate',
                ['pet_read', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_update', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_delete', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_list', [], ['offset' => 10, 'limit' => 10, 'filters' => ['name' => 'jerry'], 'sort' => ['name' => 'asc']], null, []],
                '/api/pets?offset=10&limit=10&filters%5Bname%5D=jerry&sort%5Bname%5D=asc'
            ),
            new WithReturn(
                'generate',
                ['pet_create', [], [], null, []],
                '/api/pets'
            ),
        ]);

        $petParsing = new PetParsing($parser, $urlHelper);

        /** @var PetCollectionResponse $petCollectionMinimalResponse */
        $petCollectionMinimalResponse = $petParsing->getCollectionResponseSchema($request)->parse([
            'offset' => 10,
            'limit' => 10,
            'filters' => [],
            'sort' => [],
            'items' => [
                [
                    'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
                    'createdAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
                    'updatedAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
                    'name' => 'jerry',
                    'tag' => null,
                    'vaccinations' => [],
                ],
            ],
            'count' => 1,
        ]);

        self::assertSame([
            'offset' => 10,
            'limit' => 10,
            'filters' => [
                'name' => null,
            ],
            'sort' => [
                'name' => null,
            ],
            'items' => [
                0 => [
                    'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
                    'createdAt' => '2024-01-20T09:15:00+00:00',
                    'updatedAt' => '2024-01-20T09:15:00+00:00',
                    'name' => 'jerry',
                    'tag' => null,
                    'vaccinations' => [],
                    '_type' => 'pet',
                    '_links' => [
                        'read' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'GET',
                            ],
                        ],
                        'update' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'PUT',
                            ],
                        ],
                        'delete' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'DELETE',
                            ],
                        ],
                    ],
                ],
            ],
            'count' => 1,
            '_type' => 'petCollection',
            '_links' => [
                'list' => [
                    'href' => '/api/pets?offset=10&limit=10',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'GET',
                    ],
                ],
                'create' => [
                    'href' => '/api/pets',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'POST',
                    ],
                ],
            ],
        ], $petCollectionMinimalResponse->jsonSerialize());

        /** @var PetCollectionResponse $petCollectionMaximalResponse */
        $petCollectionMaximalResponse = $petParsing->getCollectionResponseSchema($request)->parse([
            'offset' => 10,
            'limit' => 10,
            'filters' => ['name' => 'jerry'],
            'sort' => ['name' => 'asc'],
            'items' => [
                [
                    'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
                    'createdAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
                    'updatedAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
                    'name' => 'jerry',
                    'tag' => null,
                    'vaccinations' => [],
                ],
            ],
            'count' => 1,
        ]);

        self::assertSame([
            'offset' => 10,
            'limit' => 10,
            'filters' => [
                'name' => 'jerry',
            ],
            'sort' => [
                'name' => 'asc',
            ],
            'items' => [
                0 => [
                    'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
                    'createdAt' => '2024-01-20T09:15:00+00:00',
                    'updatedAt' => '2024-01-20T09:15:00+00:00',
                    'name' => 'jerry',
                    'tag' => null,
                    'vaccinations' => [],
                    '_type' => 'pet',
                    '_links' => [
                        'read' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'GET',
                            ],
                        ],
                        'update' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'PUT',
                            ],
                        ],
                        'delete' => [
                            'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'DELETE',
                            ],
                        ],
                    ],
                ],
            ],
            'count' => 1,
            '_type' => 'petCollection',
            '_links' => [
                'list' => [
                    'href' => '/api/pets?offset=10&limit=10&filters%5Bname%5D=jerry&sort%5Bname%5D=asc',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'GET',
                    ],
                ],
                'create' => [
                    'href' => '/api/pets',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'POST',
                    ],
                ],
            ],
        ], $petCollectionMaximalResponse->jsonSerialize());
    }

    public function testGetModelRequestSchema(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $parser = new Parser();

        /** @var UrlHelperInterface $urlHelper */
        $urlHelper = $builder->create(UrlHelperInterface::class, []);

        $petParsing = new PetParsing($parser, $urlHelper);

        $petRequestMinimal = $petParsing->getModelRequestSchema($request)->parse(['name' => 'jerry']);

        self::assertInstanceOf(PetRequest::class, $petRequestMinimal);

        self::assertSame([
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [],
        ], json_decode(json_encode($petRequestMinimal), true));

        $petRequestMaximal = $petParsing->getModelRequestSchema($request)->parse([
            'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
            'createdAt' => '2024-01-20T09:15:00+00:00',
            'updatedAt' => '2024-01-20T09:15:00+00:00',
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [
                ['name' => 'rabid', '_type' => ''],
                ['name' => 'cat cold', '_type' => ''],
            ],
            '_type' => '',
            '_links' => [],
        ]);

        self::assertInstanceOf(PetRequest::class, $petRequestMaximal);

        self::assertSame([
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [
                [
                    'name' => 'rabid',
                ],
                [
                    'name' => 'cat cold',
                ],
            ],
        ], json_decode(json_encode($petRequestMaximal), true));

        try {
            $petParsing->getModelRequestSchema($request)->parse([
                'name' => '',
                'tag' => '',
                'vaccinations' => [
                    ['name' => ''],
                ],
            ]);

            throw new \Exception('Expect fail');
        } catch (ErrorsException $e) {
            self::assertSame([
                [
                    'name' => 'name',
                    'reason' => 'Min length {{min}}, 0 given',
                    'details' => [
                        '_template' => 'Min length {{min}}, {{given}} given',
                        'minLength' => 1,
                        'given' => 0,
                    ],
                ],
                [
                    'name' => 'tag',
                    'reason' => 'Min length {{min}}, 0 given',
                    'details' => [
                        '_template' => 'Min length {{min}}, {{given}} given',
                        'minLength' => 1,
                        'given' => 0,
                    ],
                ],
                [
                    'name' => 'vaccinations[0][name]',
                    'reason' => 'Min length {{min}}, 0 given',
                    'details' => [
                        '_template' => 'Min length {{min}}, {{given}} given',
                        'minLength' => 1,
                        'given' => 0,
                    ],
                ],
            ], $e->errors->toApiProblemInvalidParameters());
        }
    }

    public function testGetModelResponseSchema(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        $parser = new Parser();

        /** @var UrlHelperInterface $urlHelper */
        $urlHelper = $builder->create(UrlHelperInterface::class, [
            new WithReturn(
                'generate',
                ['pet_read', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_update', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
            new WithReturn(
                'generate',
                ['pet_delete', ['id' => '019c201f-6a83-7696-9899-50fbf7b2278d'], [], null, []],
                '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d'
            ),
        ]);

        $petParsing = new PetParsing($parser, $urlHelper);

        /** @var PetResponse $petResponse */
        $petResponse = $petParsing->getModelResponseSchema($request)->parse([
            'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
            'createdAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
            'updatedAt' => new \DateTimeImmutable('2024-01-20T09:15:00+00:00'),
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [
                ['name' => 'rabid'],
                ['name' => 'cat cold'],
            ],
        ]);

        self::assertSame([
            'id' => '019c201f-6a83-7696-9899-50fbf7b2278d',
            'createdAt' => '2024-01-20T09:15:00+00:00',
            'updatedAt' => '2024-01-20T09:15:00+00:00',
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [
                0 => [
                    'name' => 'rabid',
                    '_type' => 'vaccination',
                ],
                1 => [
                    'name' => 'cat cold',
                    '_type' => 'vaccination',
                ],
            ],
            '_type' => 'pet',
            '_links' => [
                'read' => [
                    'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'GET',
                    ],
                ],
                'update' => [
                    'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'PUT',
                    ],
                ],
                'delete' => [
                    'href' => '/api/pets/019c201f-6a83-7696-9899-50fbf7b2278d',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'DELETE',
                    ],
                ],
            ],
        ], $petResponse->jsonSerialize());
    }
}
