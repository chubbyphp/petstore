<?php

declare(strict_types=1);

namespace App\Dto\Collection;

final class PetCollectionSort implements \JsonSerializable
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
