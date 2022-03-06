<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\ModelInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Slim\Interfaces\RouteParserInterface;

abstract class AbstractModelMapping implements NormalizationObjectMappingInterface
{
    public function __construct(protected RouteParserInterface $router)
    {
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::create('id')->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('createdAt', \DateTime::ATOM)->getMapping(),
            NormalizationFieldMappingBuilder::createDateTime('updatedAt', \DateTime::ATOM)->getMapping(),
        ];
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    final public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [];
    }

    /**
     * @return array<NormalizationLinkMappingInterface>
     */
    final public function getNormalizationLinkMappings(string $path): array
    {
        return [
            NormalizationLinkMappingBuilder::createCallback('read', fn (string $path, ModelInterface $model) => LinkBuilder::create(
                $this->router->urlFor($this->getReadRouteName(), ['id' => $model->getId()])
            )
                ->setAttributes(['method' => 'GET'])
                ->getLink())->getMapping(),
            NormalizationLinkMappingBuilder::createCallback('update', fn (string $path, ModelInterface $model) => LinkBuilder::create(
                $this->router->urlFor($this->getUpdateRouteName(), ['id' => $model->getId()])
            )
                ->setAttributes(['method' => 'PUT'])
                ->getLink())->getMapping(),
            NormalizationLinkMappingBuilder::createCallback('delete', fn (string $path, ModelInterface $model) => LinkBuilder::create(
                $this->router->urlFor($this->getDeleteRouteName(), ['id' => $model->getId()])
            )
                ->setAttributes(['method' => 'DELETE'])
                ->getLink())->getMapping(),
        ];
    }

    abstract protected function getReadRouteName(): string;

    abstract protected function getUpdateRouteName(): string;

    abstract protected function getDeleteRouteName(): string;
}
