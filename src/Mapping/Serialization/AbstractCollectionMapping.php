<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\CollectionInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;

abstract class AbstractCollectionMapping implements NormalizationObjectMappingInterface
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
            NormalizationFieldMappingBuilder::create('offset')->getMapping(),
            NormalizationFieldMappingBuilder::create('limit')->getMapping(),
            NormalizationFieldMappingBuilder::create('count')->getMapping(),
            NormalizationFieldMappingBuilder::create('filters')->getMapping(),
            NormalizationFieldMappingBuilder::create('sort')->getMapping(),
        ];
    }

    /**
     * @return array<NormalizationFieldMappingInterface>
     */
    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::createEmbedMany('items')->getMapping(),
        ];
    }

    /**
     * @return array<NormalizationLinkMappingInterface>
     */
    public function getNormalizationLinkMappings(string $path): array
    {
        return [
            NormalizationLinkMappingBuilder::createCallback('list', function (
                string $path,
                CollectionInterface $collection,
                NormalizerContextInterface $context
            ) {
                $queryParams = [];

                if (null !== $request = $context->getRequest()) {
                    $queryParams = $request->getQueryParams();
                }

                /** @var array<string, array|bool|float|int|string> $queryParams */
                $queryParams = \array_merge($queryParams, [
                    'offset' => $collection->getOffset(),
                    'limit' => $collection->getLimit(),
                ]);

                return LinkBuilder
                    ::create(
                        $this->router->generatePath($this->getListRouteName(), [], $queryParams)
                    )
                        ->setAttributes(['method' => 'GET'])
                        ->getLink()
                ;
            })->getMapping(),
            NormalizationLinkMappingBuilder::createCallback('create', function () {
                return LinkBuilder::create($this->router->generatePath($this->getCreateRouteName()))
                    ->setAttributes(['method' => 'POST'])
                    ->getLink()
                ;
            })->getMapping(),
        ];
    }

    abstract protected function getListRouteName(): string;

    abstract protected function getCreateRouteName(): string;
}
