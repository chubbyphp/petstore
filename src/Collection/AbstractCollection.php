<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\ModelInterface;

abstract class AbstractCollection implements CollectionInterface
{
    protected int $offset = 0;

    protected int $limit = self::LIMIT;

    /**
     * @var array<string, string>
     */
    protected array $filters = [];

    /**
     * @var array<string, string>
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
     * @param array<string, string> $filters
     */
    final public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return array<string, string>
     */
    final public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<string, string> $sort
     */
    final public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return array<string, string>
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
}
