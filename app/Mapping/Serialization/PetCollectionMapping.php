<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\PetCollection;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;

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
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        $mappings = parent::getNormalizationFieldMappings($path);
        $mappings[] = NormalizationFieldMappingBuilder::create('name')->getMapping();

        return $mappings;
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
