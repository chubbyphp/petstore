<?php

declare(strict_types=1);

namespace App\Pet\Dto\Model;

use Chubbyphp\Api\Dto\Model\ModelResponseInterface;

final readonly class PetResponse implements ModelResponseInterface
{
    /**
     * @param array<VaccinationResponse> $vaccinations
     * @param array<string, array{
     *   href: string,
     *   templated: bool,
     *   rel: array<string>,
     *   attributes: array<string, string>
     * }> $_links
     */
    public function __construct(
        public string $id,
        public string $createdAt,
        public ?string $updatedAt,
        public string $name,
        public ?string $tag,
        public array $vaccinations,
        public string $_type,
        public array $_links = [],
    ) {}

    /**
     * @return array{
     *   id: string,
     *   createdAt: string,
     *   updatedAt: null|string,
     *   name: string,
     *   tag: null|string,
     *   vaccinations: array<array{name: string, _type: string}>,
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
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'name' => $this->name,
            'tag' => $this->tag,
            'vaccinations' => array_map(
                static fn (VaccinationResponse $vaccination) => $vaccination->jsonSerialize(),
                $this->vaccinations
            ),
            '_type' => $this->_type,
            '_links' => $this->_links,
        ];
    }
}
