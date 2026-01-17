<?php

declare(strict_types=1);

namespace App\Dto\Collection;

use App\Dto\Model\ModelResponseInterface;

abstract class AbstractCollectionResponse implements CollectionResponseInterface
{
    public int $offset;

    public int $limit;

    /**
     * @var array<ModelResponseInterface>
     */
    public array $items;

    public int $count;

    public string $_type;

    /**
     * @var array<string, array{
     *   href: string,
     *   templated: bool,
     *   rel: array<string>,
     *   attributes: array<string, string>
     * }>
     */
    public array $_links;

    /**
     * @return array{
     *   offset: int,
     *   limit: int,
     *   filters: array<string, null|string>,
     *   sort: array<string, null|string>,
     *   items: array<array{
     *     id: string,
     *     createdAt: string,
     *     updatedAt: null|string,
     *     _type: string,
     *     _links: array<string, array{
     *       href: string,
     *       templated: bool,
     *       rel: array<string>,
     *       attributes: array<string, string>
     *     }>,
     *     ...
     *   }>,
     *   count: int,
     *   _links: array<string, array{
     *     href: string,
     *     templated: bool,
     *     rel: array<string>,
     *     attributes: array<string, string>
     *   }>,
     *   _type: string
     * }
     */
    final public function jsonSerialize(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'filters' => $this->getFilters()->jsonSerialize(),
            'sort' => $this->getSort()->jsonSerialize(),
            'items' => $items,
            'count' => $this->count,
            '_type' => $this->_type,
            '_links' => $this->_links,
        ];
    }

    abstract protected function getFilters(): CollectionFiltersInterface;

    abstract protected function getSort(): CollectionSortInterface;
}
