<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\ModelInterface;

abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $limit = self::LIMIT;

    /**
     * @var array<string, string>
     */
    protected $filters = [];

    /**
     * @var array<string, string>
     */
    protected $sort = [];

    /**
     * @var array<ModelInterface>
     */
    protected $items = [];

    /**
     * @var int
     */
    protected $count = 0;

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param array<string, string> $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return array<string, string>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array<string, string> $sort
     */
    public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return array<string, string>
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param array<ModelInterface> $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @return array<ModelInterface>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
