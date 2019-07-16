<?php

declare(strict_types=1);

namespace App\Mapping\Deserialization;

use App\Collection\PetCollection;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;

final class PetCollectionMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return PetCollection::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable
    {
        return function () {
            $class = $this->getClass();

            return new $class();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::createConvertType('offset', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createConvertType('limit', ConvertTypeFieldDenormalizer::TYPE_INT)
                ->getMapping(),
            DenormalizationFieldMappingBuilder::create('sort')->getMapping(),
        ];
    }
}
