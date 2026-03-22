<?php

declare(strict_types=1);

namespace App\Pet\Parsing;

use App\Pet\Dto\Collection\PetCollectionFilters;
use App\Pet\Dto\Collection\PetCollectionRequest;
use App\Pet\Dto\Collection\PetCollectionResponse;
use App\Pet\Dto\Collection\PetCollectionSort;
use App\Pet\Dto\Model\PetRequest;
use App\Pet\Dto\Model\PetResponse;
use App\Pet\Dto\Model\VaccinationRequest;
use App\Pet\Dto\Model\VaccinationResponse;
use Chubbyphp\Api\Collection\CollectionInterface;
use Chubbyphp\Api\Parsing\ParsingInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Parsing\Enum\Uuid;
use Chubbyphp\Parsing\ParserInterface;
use Chubbyphp\Parsing\Schema\ObjectSchemaInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PetParsing implements ParsingInterface
{
    private ?ObjectSchemaInterface $collectionRequestSchema = null;

    private ?ObjectSchemaInterface $collectionResponseSchema = null;

    private ?ObjectSchemaInterface $modelRequestSchema = null;

    private ?ObjectSchemaInterface $modelResponseSchema = null;

    public function __construct(
        private readonly ParserInterface $parser,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    public function getCollectionRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->collectionRequestSchema) {
            $p = $this->parser;

            $this->collectionRequestSchema = $p->object([
                'offset' => $p->union([$p->string()->toInt(), $p->int()->default(0)]),
                'limit' => $p->union([
                    $p->string()->toInt(),
                    $p->int()->default(CollectionInterface::LIMIT),
                ]),
                'filters' => $p->object([
                    'name' => $p->string()->nullable()->default(null),
                ], PetCollectionFilters::class, true)->strict()->default([]),
                'sort' => $p->object([
                    'name' => $p->union([
                        $p->const('asc'),
                        $p->const('desc'),
                    ])->nullable()->default(null),
                ], PetCollectionSort::class, true)->strict()->default([]),
            ], PetCollectionRequest::class, true)->strict();
        }

        return $this->collectionRequestSchema;
    }

    public function getCollectionResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->collectionResponseSchema) {
            $p = $this->parser;

            $this->collectionResponseSchema = $p->object([
                'offset' => $p->int(),
                'limit' => $p->int(),
                'filters' => $p->object([
                    'name' => $p->string()->nullable(),
                ], PetCollectionFilters::class, true)->strict(),
                'sort' => $p->object([
                    'name' => $p->union([
                        $p->const('asc'),
                        $p->const('desc'),
                    ])->nullable()->default(null),
                ], PetCollectionSort::class, true)->strict(),
                'items' => $p->array($this->getModelResponseSchema($request)),
                'count' => $p->int(),
                '_type' => $p->const('petCollection')->default('petCollection'),
            ], PetCollectionResponse::class, true)
                ->strict()
                ->postParse(function (PetCollectionResponse $petCollectionResponse) {
                    $queryParams = [
                        'offset' => $petCollectionResponse->offset,
                        'limit' => $petCollectionResponse->limit,
                        'filters' => $petCollectionResponse->filters->jsonSerialize(),
                        'sort' => $petCollectionResponse->sort->jsonSerialize(),
                    ];

                    return $petCollectionResponse->withLinks([
                        'list' => [
                            'href' => $this->urlGenerator->generatePath('pet_list', [], $queryParams),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'GET'],
                        ],
                        'create' => [
                            'href' => $this->urlGenerator->generatePath('pet_create'),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'POST'],
                        ],
                    ]);
                })
            ;
        }

        return $this->collectionResponseSchema;
    }

    public function getModelRequestSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->modelRequestSchema) {
            $p = $this->parser;

            $this->modelRequestSchema = $p->object([
                'name' => $p->string()->minLength(1),
                'tag' => $p->string()->minLength(1)->nullable(),
                'vaccinations' => $p->array($p->object([
                    'name' => $p->string()->minLength(1),
                ], VaccinationRequest::class, true)->strict(['_type']))->default([]),
            ], PetRequest::class, true)->strict(['id', 'createdAt', 'updatedAt', '_type', '_links']);
        }

        return $this->modelRequestSchema;
    }

    public function getModelResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->modelResponseSchema) {
            $p = $this->parser;

            $this->modelResponseSchema = $p->object([
                'id' => $p->string()->uuid(Uuid::v7),
                'createdAt' => $p->dateTime()->toString(),
                'updatedAt' => $p->dateTime()->nullable()->toString(),
                'name' => $p->string(),
                'tag' => $p->string()->nullable(),
                'vaccinations' => $p->array($p->object([
                    'name' => $p->string(),
                    '_type' => $p->const('vaccination')->default('vaccination'),
                ], VaccinationResponse::class, true)->strict()),
                '_type' => $p->const('pet')->default('pet'),
            ], PetResponse::class, true)->strict()
                ->postParse(
                    fn (PetResponse $petResponse) => $petResponse->withLinks([
                        'read' => [
                            'href' => $this->urlGenerator->generatePath('pet_read', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'GET'],
                        ],
                        'update' => [
                            'href' => $this->urlGenerator->generatePath('pet_update', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'PUT'],
                        ],
                        'delete' => [
                            'href' => $this->urlGenerator->generatePath('pet_delete', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'DELETE'],
                        ],
                    ]),
                )
            ;
        }

        return $this->modelResponseSchema;
    }
}
