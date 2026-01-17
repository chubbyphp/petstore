<?php

declare(strict_types=1);

namespace App\Dto\Model;

interface ModelResponseInterface extends \JsonSerializable
{
    /**
     * @return array{
     *   id: string,
     *   createdAt: string,
     *   updatedAt: null|string,
     *   _type: string,
     *   _links: array<string, array{
     *     href: string,
     *     templated: bool,
     *     rel: array<string>,
     *     attributes: array<string, string>
     *   }>,
     *   ...
     * }
     */
    public function jsonSerialize(): array;
}
