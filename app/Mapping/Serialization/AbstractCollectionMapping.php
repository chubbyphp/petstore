<?php

declare(strict_types=1);

namespace App\Mapping\Serialization;

use App\Collection\CollectionInterface;
use Chubbyphp\Serialization\Link\LinkBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMapping;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Mapping\NormalizationObjectMappingInterface;
use Chubbyphp\Serialization\Normalizer\CallbackLinkNormalizer;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Slim\Interfaces\RouteParserInterface;

abstract class AbstractCollectionMapping implements NormalizationObjectMappingInterface
{
    /**
     * @var RouteParserInterface
     */
    protected $router;

    /**
     * @param RouteParserInterface $router
     */
    public function __construct(RouteParserInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
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
     * @param string $path
     *
     * @return NormalizationFieldMappingInterface[]
     */
    public function getNormalizationEmbeddedFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::createEmbedMany('items')->getMapping(),
        ];
    }

    /**
     * @param string $path
     *
     * @return NormalizationLinkMappingInterface[]
     */
    public function getNormalizationLinkMappings(string $path): array
    {
        return [
            new NormalizationLinkMapping('list', [], new CallbackLinkNormalizer(
                function (string $path, CollectionInterface $collection, NormalizerContextInterface $context) {
                    return LinkBuilder
                        ::create(
                            $this->router->urlFor(
                                $this->getListRouteName(),
                                [],
                                array_replace($context->getRequest()->getQueryParams(), [
                                    'offset' => $collection->getOffset(),
                                    'limit' => $collection->getLimit(),
                                ])
                            )
                        )
                        ->setAttributes(['method' => 'GET'])
                        ->getLink()
                    ;
                }
            )),
            new NormalizationLinkMapping('create', [], new CallbackLinkNormalizer(
                function () {
                    return LinkBuilder::create($this->router->urlFor($this->getCreateRouteName()))
                        ->setAttributes(['method' => 'POST'])
                        ->getLink()
                    ;
                }
            )),
        ];
    }

    /**
     * @return string
     */
    abstract protected function getListRouteName(): string;

    /**
     * @return string
     */
    abstract protected function getCreateRouteName(): string;
}
