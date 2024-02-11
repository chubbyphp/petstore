<?php

declare(strict_types=1);

namespace App\Model;

final class Vaccination
{
    private ?string $name = null;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array{name: null|string}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
