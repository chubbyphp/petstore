<?php

declare(strict_types=1);

namespace App\Factory\Model;

use App\Model\ModelInterface;
use App\Model\Pet;

final class PetFactory implements FactoryInterface
{
    /**
     * @return ModelInterface
     */
    public function create(): ModelInterface
    {
        return new Pet();
    }
}
