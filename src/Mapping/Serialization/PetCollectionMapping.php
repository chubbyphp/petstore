<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\PetCollection;

final class PetCollectionMapping extends AbstractCollectionMapping
{
    public function getClass(): string
    {
        return PetCollection::class;
    }

    public function getNormalizationType(): string
    {
        return 'petCollection';
    }

    protected function getListRouteName(): string
    {
        return 'pet_list';
    }

    protected function getCreateRouteName(): string
    {
        return 'pet_create';
    }
}
