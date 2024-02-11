<?php

declare(strict_types=1);

namespace App\Dto\Collection;

use App\Dto\Model\PetResponse;

final class PetCollectionResponse
{
    public int $offset;

    public int $limit;

    public PetCollectionFilters $filters;

    public PetCollectionSort $sort;

    /**
     * @var array<PetResponse>
     */
    public array $items;

    public int $count;

    public string $_type;

    /**
     * @var array<string, array{
     *   href: string,
     *   templated:bool,
     *   rel: array<string>,
     *   attributes: array<string, string>
     * }>
     */
    public array $_links;

    /**
     * @return array{
     *   offset:int,
     *   limit:int,
     *   filters:array{name: null|string},
     *   sort:array{name: null|string},
     *   items: array<array{
     *     id:string,
     *     createdAt:string,
     *     updatedAt:null|string,
     *     name:string, tag:null|string,
     *     vaccinations: array<array{name:string, _type: string}>,
     *     _type: string,
     *     _links: array<string, array{
     *       href: string,
     *       templated:bool,
     *       rel: array<string>,
     *       attributes: array<string, string>
     *     }>
     *   }>,
     *   count: int,
     *   _type: string
     * }
     */
    public function jsonSerialize(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'filters' => $this->filters->jsonSerialize(),
            'sort' => $this->sort->jsonSerialize(),
            'items' => $items,
            'count' => $this->count,
            '_type' => $this->_type,
            '_links' => $this->_links,
        ];
    }
}
