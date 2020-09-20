<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Model\ModelInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;

abstract class AbstractModelMapping implements NormalizationObjectMappingInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [];
    }

    /**
     * @return array<NormalizationLinkMappingInterface>
     */
    public function getNormalizationLinkMappings(string $path): array
    {
        return [
            NormalizationLinkMappingBuilder::createCallback('read', function (string $path, ModelInterface $model) {
                return LinkBuilder
                    ::create(
                        $this->router->generatePath($this->getReadRouteName(), ['id' => $model->getId()])
                    )
                        ->setAttributes(['method' => 'GET'])
                        ->getLink()
                ;
            })->getMapping(),
            NormalizationLinkMappingBuilder::createCallback('update', function (string $path, ModelInterface $model) {
                return LinkBuilder
                    ::create(
                        $this->router->generatePath($this->getUpdateRouteName(), ['id' => $model->getId()])
                    )
                        ->setAttributes(['method' => 'PUT'])
                        ->getLink()
                ;
            })->getMapping(),
            NormalizationLinkMappingBuilder::createCallback('delete', function (string $path, ModelInterface $model) {
                return LinkBuilder
                    ::create(
                        $this->router->generatePath($this->getDeleteRouteName(), ['id' => $model->getId()])
                    )
                        ->setAttributes(['method' => 'DELETE'])
                        ->getLink()
                ;
            })->getMapping(),
        ];
    }

    abstract protected function getReadRouteName(): string;

    abstract protected function getUpdateRouteName(): string;

    abstract protected function getDeleteRouteName(): string;
}
