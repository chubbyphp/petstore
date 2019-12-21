<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \App\Mapping\Serialization\PetMapping
 *
 * @internal
 */
final class PetMappingTest extends ModelMappingTest
{
    use MockByCallsTrait;

    public function testGetNormalizationFieldMappings(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        $mapping = $this->getModelMapping($router);

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        self::assertEquals([
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::create('name')->getMapping(),
            NormalizationFieldMappingBuilder::create('tag')->getMapping(),
            NormalizationFieldMappingBuilder::createEmbedMany('vaccinations')->getMapping(),
        ], $fieldMappings);
    }

    protected function getClass(): string
    {
        return Pet::class;
    }

    protected function getNormalizationType(): string
    {
        return 'pet';
    }

    protected function getReadRoute(): string
    {
        return 'pet_read';
    }

    protected function getUpdateRoute(): string
    {
        return 'pet_update';
    }

    protected function getDeleteRoute(): string
    {
        return 'pet_delete';
    }

    protected function getModelPath(): string
    {
        return '/api/pets/%s';
    }

    protected function getModelMapping(RouterInterface $router): AbstractModelMapping
    {
        return new PetMapping($router);
    }
}
