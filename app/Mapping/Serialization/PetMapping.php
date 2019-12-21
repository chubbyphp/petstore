<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\Pet;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;

final class PetMapping extends AbstractModelMapping
{
    public function getClass(): string
    {
        return Pet::class;
    }

    public function getNormalizationType(): string
    {
        return 'pet';
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        $normalizationFieldMappings = parent::getNormalizationFieldMappings($path);
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('name')->getMapping();
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::create('tag')->getMapping();
        $normalizationFieldMappings[] = NormalizationFieldMappingBuilder::createEmbedMany('vaccinations')->getMapping();

        return $normalizationFieldMappings;
    }

    protected function getReadRouteName(): string
    {
        return 'pet_read';
    }

    protected function getUpdateRouteName(): string
    {
        return 'pet_update';
    }

    protected function getDeleteRouteName(): string
    {
        return 'pet_delete';
    }
}
