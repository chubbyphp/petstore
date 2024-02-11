<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\ModelInterface;

abstract class AbstractCollection implements CollectionInterface
{
    protected int $offset = 0;

    protected int $limit = self::LIMIT;

    /**
     * @var array<string, null|string>
     */
    protected array $filters = [];

    /**
     * @var array<string, null|string>
     */
    protected array $sort = [];

    /**
     * @var array<ModelInterface>
     */
    protected array $items = [];

    protected int $count = 0;

    final public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    final public function getOffset(): int
    {
        return $this->offset;
    }

    final public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    final public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param array<string, null|string> $filters
     */
    final public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return array<string, null|string>
     */
    final public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<string, null|string> $sort
     */
    final public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return array<string, null|string>
     */
    final public function getSort(): array
    {
        return $this->sort;
    }

    final public function setCount(int $count): void
    {
        $this->count = $count;
    }

    final public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param array<ModelInterface> $items
     */
    final public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @return array<ModelInterface>
     */
    final public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array{offset: int, limit: int, filters: array<string, null|string>, sort: array<string, null|string>, items: array<ModelInterface>, count: int}
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
            'filters' => $this->filters,
            'sort' => $this->sort,
            'items' => $items,
            'count' => $this->count,
        ];
    }
}
