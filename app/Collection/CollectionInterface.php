<?php

declare(strict_types=1);

namespace App\Collection;

interface CollectionInterface
{
    const LIMIT = 20;

    /**
     * @param int $offset
     */
    public function setOffset(int $offset);

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @param int $limit
     */
    public function setLimit(int $limit);

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @param string[] $filters
     */
    public function setFilters(array $filters);

    /**
     * @return string[]
     */
    public function getFilters(): array;

    /**
     * @param string[] $sort
     */
    public function setSort(array $sort);

    /**
     * @return string[]
     */
    public function getSort(): array;

    /**
     * @param int $count
     */
    public function setCount(int $count): void;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @param array
     */
    public function setItems(array $items): void;

    /**
     * @return array
     */
    public function getItems(): array;
}
