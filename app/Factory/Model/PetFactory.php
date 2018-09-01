<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Model\Pet;

final class PetFactory implements ModelFactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return Pet::class;
    }
}
