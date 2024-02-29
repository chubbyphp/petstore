<?php

declare(strict_types=1);

namespace App\Dto\Model;

final class PetResponse implements \JsonSerializable
{
    public string $id;

    public string $createdAt;

    public ?string $updatedAt;

    public string $name;

    public ?string $tag;

    /**
     * @var array<VaccinationResponse>
     */
    public array $vaccinations;

    public string $_type;

    /**
     * @var array<string, array{
     *   href: string,
     *   templated:bool,
     *   rel: array<string>,
     *   attributes: array<string, string>
     * }>
     */
    public array $_links;

    /**
     * @return array{
     *   id:string,
     *   createdAt:string,
     *   updatedAt:null|string,
     *   name:string, tag:null|string,
     *   vaccinations: array<array{name:string, _type: string}>,
     *   _type: string,
     *   _links: array<string, array{
     *     href: string,
     *     templated:bool,
     *     rel: array<string>,
     *     attributes: array<string, string>
     *   }>
     * }
     */
    public function jsonSerialize(): array
    {
        $vaccinations = [];
        foreach ($this->vaccinations as $vaccination) {
            $vaccinations[] = $vaccination->jsonSerialize();
        }

        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'name' => $this->name,
            'tag' => $this->tag,
            'vaccinations' => $vaccinations,
            '_type' => $this->_type,
            '_links' => $this->_links,
        ];
    }
}
