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
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PetParsing implements ParsingInterface
{
    private null|ObjectSchemaInterface $collectionRequestSchema = null;

    private null|ObjectSchemaInterface $collectionResponseSchema = null;

    private null|ObjectSchemaInterface $modelRequestSchema = null;

    private null|ObjectSchemaInterface $modelResponseSchema = null;

    public function __construct(
        private ParserInterface $parser,
        private RouterInterface $router,
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
                ], PetCollectionFilters::class)->strict()->default([]),
                'sort' => $p->object([
                    'name' => $p->union([$p->literal('asc'), $p->literal('desc')])->nullable()->default(null),
                ], PetCollectionSort::class)->strict()->default([]),
            ], PetCollectionRequest::class)->strict();
        }

        return $this->collectionRequestSchema;
    }

    public function getCollectionResponseSchema(ServerRequestInterface $request): ObjectSchemaInterface
    {
        if (null === $this->collectionResponseSchema) {
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
                ->postParse(function (PetCollectionResponse $petCollectionResponse) {
                    $queryParams = [
                        'offset' => $petCollectionResponse->offset,
                        'limit' => $petCollectionResponse->limit,
                        'filters' => $petCollectionResponse->filters->jsonSerialize(),
                        'sort' => $petCollectionResponse->sort->jsonSerialize(),
                    ];

                    $petCollectionResponse->_links = [
                        'list' => [
                            'href' => $this->router->generateUri('pet_list', []).'?'.http_build_query($queryParams),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'GET'],
                        ],
                        'create' => [
                            'href' => $this->router->generateUri('pet_create'),
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
                            'href' => $this->router->generateUri('pet_read', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'GET'],
                        ],
                        'update' => [
                            'href' => $this->router->generateUri('pet_update', ['id' => $petResponse->id]),
                            'templated' => false,
                            'rel' => [],
                            'attributes' => ['method' => 'PUT'],
                        ],
                        'delete' => [
                            'href' => $this->router->generateUri('pet_delete', ['id' => $petResponse->id]),
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
