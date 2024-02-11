<?php

declare(strict_types=1);

namespace App\Dto\Model;

final class VaccinationResponse implements \JsonSerializable
{
    public string $name;

    public string $_type;

    /**
     * @return array{
     *    name:string,
     *   _type: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            '_type' => $this->_type,
        ];
    }
}
