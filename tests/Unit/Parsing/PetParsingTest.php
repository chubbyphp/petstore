<?php

declare(strict_types=1);

namespace App\Tests\Unit\Parsing;

use App\Dto\Collection\PetCollectionRequest;
use App\Dto\Model\PetRequest;
use App\Parsing\PetParsing;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Parsing\Parser;
use Chubbyphp\Parsing\ParserErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\Parsing\PetParsing
 *
 * @internal
 */
final class PetParsingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetCollectionRequestSchema(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $parser = new Parser();

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class);

        $petParsing = new PetParsing($parser, $urlGenerator);

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
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $parser = new Parser();

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class, [
            Call::create('generatePath')
                ->with('pet_read', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
            Call::create('generatePath')
                ->with('pet_update', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
            Call::create('generatePath')
                ->with('pet_delete', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
            Call::create('generatePath')->with('pet_list', [], ['offset' => 10, 'limit' => 10, 'filters' => ['name' => 'jerry'], 'sort' => ['name' => 'asc']])
                ->willReturn('/api/pets?offset=10&limit=10'),
            Call::create('generatePath')->with('pet_create', [], [])->willReturn('/api/pets'),
        ]);

        $petParsing = new PetParsing($parser, $urlGenerator);

        $data = $petParsing->getCollectionResponseSchema($request)->parse([
            'offset' => 10,
            'limit' => 10,
            'filters' => ['name' => 'jerry'],
            'sort' => ['name' => 'asc'],
            'items' => [
                [
                    'id' => 'f8b51629-d105-401e-8872-bebd9911709a',
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
                    'id' => 'f8b51629-d105-401e-8872-bebd9911709a',
                    'createdAt' => '2024-01-20T09:15:00+00:00',
                    'updatedAt' => '2024-01-20T09:15:00+00:00',
                    'name' => 'jerry',
                    'tag' => null,
                    'vaccinations' => [],
                    '_type' => 'pet',
                    '_links' => [
                        'read' => [
                            'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'GET',
                            ],
                        ],
                        'update' => [
                            'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
                            'templated' => false,
                            'rel' => [],
                            'attributes' => [
                                'method' => 'PUT',
                            ],
                        ],
                        'delete' => [
                            'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
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
        ], $data);
    }

    public function testGetModelRequestSchema(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $parser = new Parser();

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class);

        $petParsing = new PetParsing($parser, $urlGenerator);

        $petRequestMinimal = $petParsing->getModelRequestSchema($request)->parse(['name' => 'jerry']);

        self::assertInstanceOf(PetRequest::class, $petRequestMinimal);

        self::assertSame([
            'name' => 'jerry',
            'tag' => null,
            'vaccinations' => [],
        ], json_decode(json_encode($petRequestMinimal), true));

        $petRequestMaximal = $petParsing->getModelRequestSchema($request)->parse([
            'id' => 'f8b51629-d105-401e-8872-bebd9911709a',
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
        } catch (ParserErrorException $e) {
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
            ], $e->getApiProblemErrorMessages());
        }
    }

    public function testGetModelResponseSchema(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $parser = new Parser();

        /** @var MockObject|UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class, [
            Call::create('generatePath')
                ->with('pet_read', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
            Call::create('generatePath')
                ->with('pet_update', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
            Call::create('generatePath')
                ->with('pet_delete', ['id' => 'f8b51629-d105-401e-8872-bebd9911709a'], [])
                ->willReturn('/api/pets/f8b51629-d105-401e-8872-bebd9911709a'),
        ]);

        $petParsing = new PetParsing($parser, $urlGenerator);

        $data = $petParsing->getModelResponseSchema($request)->parse([
            'id' => 'f8b51629-d105-401e-8872-bebd9911709a',
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
            'id' => 'f8b51629-d105-401e-8872-bebd9911709a',
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
                    'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'GET',
                    ],
                ],
                'update' => [
                    'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'PUT',
                    ],
                ],
                'delete' => [
                    'href' => '/api/pets/f8b51629-d105-401e-8872-bebd9911709a',
                    'templated' => false,
                    'rel' => [],
                    'attributes' => [
                        'method' => 'DELETE',
                    ],
                ],
            ],
        ], $data);
    }
}
