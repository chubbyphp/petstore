<?php

declare(strict_types=1);

namespace App\Pet\Collection;

use App\Pet\Model\Pet;
use Chubbyphp\Api\Collection\AbstractCollection;

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
 *     }>
 *   }>,
 *   count: int,
 * }
 *
 * @method void                 setItems(array<Pet> $items)
 * @method array<Pet>           getItems()
 * @method JsonSerializedResult jsonSerialize()
 */
final class PetCollection extends AbstractCollection {}
