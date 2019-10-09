<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\ModelInterface;

interface CollectionInterface
{
    public const LIMIT = 20;

    public function setOffset(int $offset): void;

    public function getOffset(): int;

    public function setLimit(int $limit): void;

    public function getLimit(): int;

    /**
     * @param array<string, string> $filters
     */
    public function setFilters(array $filters): void;

    /**
     * @return array<string, string>
     */
    public function getFilters(): array;

    /**
     * @param array<string, string> $sort
     */
    public function setSort(array $sort): void;

    /**
     * @return array<string, string>
     */
    public function getSort(): array;

    public function setCount(int $count): void;

    public function getCount(): int;

    /**
     * @param array<ModelInterface> $items
     */
    public function setItems(array $items): void;

    /**
     * @return array<ModelInterface>
     */
    public function getItems(): array;
}
