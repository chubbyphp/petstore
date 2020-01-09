<?php

declare(strict_types=1);

namespace App\Model;

use Ramsey\Uuid\Uuid;

final class Vaccination
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Pet|null
     */
    private $pet;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setPet(?Pet $pet): void
    {
        $this->pet = $pet;
    }
}
