<?php

declare(strict_types=1);

namespace App\ServiceFactory\Factory\Model;

use App\Factory\Model\PetFactory;

final class PetFactoryFactory
{
    public function __invoke(): PetFactory
    {
        return new PetFactory();
    }
}
