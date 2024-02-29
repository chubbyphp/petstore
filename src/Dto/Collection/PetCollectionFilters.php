<?php

declare(strict_types=1);

namespace App\Dto\Collection;

final class PetCollectionFilters implements \JsonSerializable
{
    public ?string $name;

    /**
     * @return array{name: null|string}
     */
    public function jsonSerialize(): array
    {
        return ['name' => $this->name];
    }
}
