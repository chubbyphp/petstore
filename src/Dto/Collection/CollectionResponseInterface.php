<?php

declare(strict_types=1);

namespace App\Dto\Collection;

interface CollectionResponseInterface extends \JsonSerializable
{
    /**
     * @return array{
     *   offset: int,
     *   limit: int,
     *   filters: array<string, null|string>,
     *   sort: array<string, null|string>,
     *   items: array<array{
     *     id: string,
     *     createdAt: string,
     *     updatedAt: null|string,
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
     *   _type: string,
     * }
     */
    public function jsonSerialize(): array;
}
