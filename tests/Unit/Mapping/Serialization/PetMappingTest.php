<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \App\Mapping\Serialization\PetMapping
 */
final class PetMappingTest extends ModelMappingTest
{
    use MockByCallsTrait;

    public function testGetNormalizationFieldMappings(): void
    {
        /** @var UrlGeneratorInterface|MockObject $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class);

        $mapping = $this->getModelMapping($urlGenerator);

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
            NormalizationFieldMappingBuilder::create('tag')->getMapping(),
        ], $fieldMappings);
    }

    /**
     * @return string
     */
    protected function getClass(): string
    {
        return Pet::class;
    }

    /**
     * @return string
     */
    protected function getNormalizationType(): string
    {
        return 'pet';
    }

    /**
     * @return string
     */
    protected function getReadRoute(): string
    {
        return 'pet_read';
    }

    /**
     * @return string
     */
    protected function getUpdateRoute(): string
    {
        return 'pet_update';
    }

    /**
     * @return string
     */
    protected function getDeleteRoute(): string
    {
        return 'pet_delete';
    }

    /**
     * @return string
     */
    protected function getModelPath(): string
    {
        return '/api/pets/%s';
    }

    /**
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return AbstractModelMapping
     */
    protected function getModelMapping(UrlGeneratorInterface $urlGenerator): AbstractModelMapping
    {
        return new PetMapping($urlGenerator);
    }
}
