<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Pet;

final class PetRepository extends AbstractRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Pet::class;
    }
}
