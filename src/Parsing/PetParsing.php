<?php

declare(strict_types=1);

namespace App\Parsing;

use App\Collection\CollectionInterface;
use App\Dto\Collection\PetCollectionFilters;
use App\Dto\Collection\PetCollectionRequest;
use App\Dto\Collection\PetCollectionResponse;
use App\Dto\Collection\PetCollectionSort;
use App\Dto\Model\PetRequest;
use App\Dto\Model\PetResponse;
use App\Dto\Model\VaccinationRequest;
use App\Dto\Model\VaccinationResponse;
use Chubbyphp\Parsing\ParserInterface;
use Chubbyphp\Parsing\Schema\ObjectSchemaInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;

final class PetParsing implements ParsingInterface
{
    private null|ObjectSchemaInterface $collectionRequestSchema = null;

    private null|ObjectSchemaInterface $modelRequestSchema = null;

    private null|ObjectSchemaInterface $modelResponseSchema = null;

    public function __construct(
        private ParserInterface $parser,
        private RouteParserInterface $routeParser,
    ) {}

    public function getCollectionRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->collectionRequestSchema) {
            $p = $this->parser;

            $this->collectionRequestSchema = $p->object([
                'offset' => $p->union([$p->string()->default('0')->toInt(), $p->int()->default(0)]),
                'limit' => $p->union([
                    $p->string()->default((string) CollectionInterface::LIMIT)->toInt(),
                    $p->int()->default(CollectionInterface::LIMIT),
                ]),
                'filters' => $p->object([
                    'name' => $p->string()->nullable(),
                ], PetCollectionFilters::class)->strict()->default([]),
                'sort' => $p->object([
                    'name' => $p->union([$p->literal('asc'), $p->literal('desc')]),
                ], PetCollectionSort::class)->strict()->default([]),
            ], PetCollectionRequest::class)->strict();
        }

        return $this->collectionRequestSchema;
    }

    public function getCollectionResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        $p = $this->parser;

        return $p->object([
            'offset' => $p->int(),
            'limit' => $p->int(),
            'filters' => $p->object([
                'name' => $p->string()->nullable(),
            ], PetCollectionFilters::class)->strict(),
            'sort' => $p->object([
                'name' => $p->union([$p->literal('asc'), $p->literal('desc')]),
            ], PetCollectionSort::class)->strict(),
            'items' => $p->array($this->getModelResponseSchema($request)),
            'count' => $p->int(),
            '_type' => $p->literal('petCollection')->default('petCollection'),
        ], PetCollectionResponse::class)
            ->strict()
            ->postParse(function (PetCollectionResponse $petCollectionResponse) use ($request) {
                $queryParams = $request->getQueryParams();

                /** @var array{offset: string, limit: string} $queryParams */
                $queryParams = array_merge($queryParams, [
                    'offset' => $petCollectionResponse->offset,
                    'limit' => $petCollectionResponse->limit,
                ]);

                $petCollectionResponse->_links = [
                    'list' => [
                        'href' => $this->routeParser->urlFor('pet_list', [], $queryParams),
                        'templated' => false,
                        'rel' => [],
                        'attributes' => ['method' => 'GET'],
                    ],
                    'create' => [
                        'href' => $this->routeParser->urlFor('pet_create'),
                        'templated' => false,
                        'rel' => [],
                        'attributes' => ['method' => 'POST'],
                    ],
                ];

                return $petCollectionResponse;
            })
            ->postParse(static function (object $object): array {
                /** @var string $json */
                $json = json_encode($object);

                return json_decode($json, true);
            })
        ;
    }

    public function getModelRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->modelRequestSchema) {
            $p = $this->parser;

            $this->modelRequestSchema = $p->object([
                'name' => $p->string()->minLength(1),
                'tag' => $p->string()->minLength(1)->nullable(),
                'vaccinations' => $p->array($p->object([
                    'name' => $p->string(),
                ], VaccinationRequest::class)->strict(['_type']))->default([]),
            ], PetRequest::class)->strict(['id', 'createdAt', 'updatedAt', '_type', '_links']);
        }

        return $this->modelRequestSchema;
    }

    public function getModelResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->modelResponseSchema) {
            $p = $this->parser;

            $this->modelResponseSchema = $p->object([
                'id' => $p->string(),
                'createdAt' => $p->dateTime()->toString(),
                'updatedAt' => $p->dateTime()->nullable()->toString(),
                'name' => $p->string(),
                'tag' => $p->string()->nullable(),
                'vaccinations' => $p->array($p->object([
                    'name' => $p->string(),
                    '_type' => $p->literal('vaccination')->default('vaccination'),
                ], VaccinationResponse::class)->strict()),
                '_type' => $p->literal('pet')->default('pet'),
            ], PetResponse::class)->strict()
                ->postParse(function (PetResponse $petResponse) {
                    $petResponse->_links = [
                        'read' => [
                            'href' => $this->routeParser->urlFor('pet_read', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'GET'],
                        ],
                        'update' => [
                            'href' => $this->routeParser->urlFor('pet_update', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'PUT'],
                        ],
                        'delete' => [
                            'href' => $this->routeParser->urlFor('pet_delete', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'DELETE'],
                        ],
                    ];

                    return $petResponse;
                })
                ->postParse(static function (object $object): array {
                    /** @var string $json */
                    $json = json_encode($object);

                    return json_decode($json, true);
                })
            ;
        }

        return $this->modelResponseSchema;
    }
}
