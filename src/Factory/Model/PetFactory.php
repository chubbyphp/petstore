<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Factory\ModelFactoryInterface;
use App\Model\Pet;

final class PetFactory implements ModelFactoryInterface
{
    public function create(): Pet
    {
        return new Pet();
    }

    public function getClass(): string
    {
        return Pet::class;
    }
}
