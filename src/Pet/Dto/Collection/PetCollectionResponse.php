<?php

declare(strict_types=1);

namespace App\Pet\Dto\Collection;

use App\Pet\Dto\Model\PetResponse;
use Chubbyphp\Api\Dto\Collection\AbstractReadonlyCollectionResponse;

/**
 * @property PetCollectionFilters $filters
 * @property PetCollectionSort    $sort
 * @property array<PetResponse>   $items
 *
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
final readonly class PetCollectionResponse extends AbstractReadonlyCollectionResponse
{
    public function __construct(
        int $offset,
        int $limit,
        PetCollectionFilters $filters,
        PetCollectionSort $sort,
        array $items,
        int $count,
        string $_type,
        array $_links = [],
    ) {
        parent::__construct(
            $offset,
            $limit,
            $filters,
            $sort,
            $items,
            $count,
            $_type,
            $_links,
        );
    }
}
