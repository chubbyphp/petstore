<?php

declare(strict_types=1);

namespace App\Pet\Dto\Collection;

use Chubbyphp\Api\Dto\Collection\CollectionFiltersInterface;

final class PetCollectionFilters implements CollectionFiltersInterface
{
    public ?string $name = null;

    /**
     * @return array{name: null|string}
     */
    public function jsonSerialize(): array
    {
        return ['name' => $this->name];
    }
}
