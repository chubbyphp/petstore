<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\Vaccination;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;

final class VaccinationMapping implements NormalizationObjectMappingInterface
{
    public function getClass(): string
    {
        return Vaccination::class;
    }

    public function getNormalizationType(): string
    {
        return 'vaccination';
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
        ];
    }

    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [];
    }

    public function getNormalizationLinkMappings(string $path): array
    {
        return [];
    }
}
