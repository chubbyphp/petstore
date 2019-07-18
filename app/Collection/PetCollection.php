<?php

declare(strict_types=1);

namespace App\Collection;

final class PetCollection extends AbstractCollection
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @param string|null $name
     */
    public function setName(string $name = null): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
}
