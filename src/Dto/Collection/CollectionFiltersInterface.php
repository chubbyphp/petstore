<?php

declare(strict_types=1);

namespace App\Dto\Collection;

interface CollectionFiltersInterface extends \JsonSerializable
{
    /**
     * @return array<string, null|string>
     */
    public function jsonSerialize(): array;
}
