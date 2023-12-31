<?php

declare(strict_types=1);

namespace App\Mapping\Deserialization;

use App\Model\Pet;
use App\Model\Vaccination;
use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;

final class PetMapping implements DenormalizationObjectMappingInterface
{
    public function __construct(private DenormalizationFieldMappingFactoryInterface $denormalizationFieldMappingFactory) {}

    public function getClass(): string
    {
        return Pet::class;
    }

    public function getDenormalizationFactory(string $path, ?string $type = null): callable
    {
        return function () {
            $class = $this->getClass();

            return new $class();
        };
    }

    /**
     * @return array<DenormalizationFieldMappingInterface>
     */
    public function getDenormalizationFieldMappings(string $path, ?string $type = null): array
    {
        return [
            $this->denormalizationFieldMappingFactory->createConvertType('name', ConvertTypeFieldDenormalizer::TYPE_STRING),
            $this->denormalizationFieldMappingFactory->createConvertType('tag', ConvertTypeFieldDenormalizer::TYPE_STRING),
            $this->denormalizationFieldMappingFactory->create(
                'vaccinations',
                false,
                new EmbedManyFieldDenormalizer(Vaccination::class, new MethodAccessor('vaccinations'))
            ),
        ];
    }
}
