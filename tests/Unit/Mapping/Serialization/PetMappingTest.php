<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Mapping\Serialization\PetMapping;
use App\Model\Pet;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Slim\Interfaces\RouteParserInterface;

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
        /** @var RouteParserInterface|MockObject $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        $mapping = $this->getModelMapping($router);

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
     * @param RouteParserInterface $router
     *
     * @return AbstractModelMapping
     */
    protected function getModelMapping(RouteParserInterface $router): AbstractModelMapping
    {
        return new PetMapping($router);
    }
}
