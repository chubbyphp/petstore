<?php

declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\Uuid;

final class Vaccination implements \JsonSerializable
{
    private string $id;

    private ?string $name = null;

    private ?Pet $pet = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setPet(?Pet $pet): void
    {
        $this->pet = $pet;
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
