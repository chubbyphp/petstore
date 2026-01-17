<?php

declare(strict_types=1);

namespace App\Dto\Collection;

use App\Dto\Model\PetResponse;

/**
 * @phpstan-type JsonSerializedResult array{
 *   offset: int,
 *   limit: int,
 *   filters: array{name: null|string},
 *   sort: array{name: null|string},
 *   items: array<array{
 *     id: string,
 *     createdAt: string,
 *     updatedAt: null|string,
 *     name: string,
 *     tag: null|string,
 *     vaccinations: array<array{
 *       name: string,
 *       _type: string
 *     }>,
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
 *
 * @method JsonSerializedResult jsonSerialize()
 */
final class PetCollectionResponse extends AbstractCollectionResponse
{
    public PetCollectionFilters $filters;

    public PetCollectionSort $sort;

    /**
     * @var array<PetResponse>
     */
    public array $items;

    protected function getFilters(): PetCollectionFilters
    {
        return $this->filters;
    }

    protected function getSort(): PetCollectionSort
    {
        return $this->sort;
    }
}
