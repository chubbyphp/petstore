<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Factory\Collection\PetCollectionFactory;
use App\Factory\Model\PetFactory;

final class FactoryServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            PetCollectionFactory::class => static function () {
                return new PetCollectionFactory();
            },
            PetFactory::class => static function () {
                return new PetFactory();
            },
        ];
    }
}
