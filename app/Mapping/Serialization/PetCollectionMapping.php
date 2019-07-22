<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\PetCollection;

final class PetCollectionMapping extends AbstractCollectionMapping
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return PetCollection::class;
    }

    /**
     * @return string
     */
    public function getNormalizationType(): string
    {
        return 'petCollection';
    }

    /**
     * @return string
     */
    protected function getListRouteName(): string
    {
        return 'pet_list';
    }

    /**
     * @return string
     */
    protected function getCreateRouteName(): string
    {
        return 'pet_create';
    }
}
