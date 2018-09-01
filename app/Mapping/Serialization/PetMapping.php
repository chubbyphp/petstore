<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\Pet;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;

final class PetMapping extends AbstractModelMapping
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Pet::class;
    }

    /**
     * @return string
     */
    public function getNormalizationType(): string
    {
        return 'pet';
    }

    /**
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        $normalizationFieldMappings = parent::getNormalizationFieldMappings($path);
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('name')->getMapping();
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('tag')->getMapping();

        return $normalizationFieldMappings;
    }

    /**
     * @return string
     */
    protected function getReadRouteName(): string
    {
        return 'pet_read';
    }

    /**
     * @return string
     */
    protected function getUpdateRouteName(): string
    {
        return 'pet_update';
    }

    /**
     * @return string
     */
    protected function getDeleteRouteName(): string
    {
        return 'pet_delete';
    }
}
