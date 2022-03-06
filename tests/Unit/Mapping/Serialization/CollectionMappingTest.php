<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\AbstractCollection;
use App\Collection\CollectionInterface;
use App\Mapping\Serialization\AbstractCollectionMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\Mapping\Serialization\AbstractCollectionMapping
 *
 * @internal
 */
class CollectionMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var MockObject|RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        $mapping = $this->getCollectionMapping($router);

        static::assertSame($this->getClass(), $mapping->getClass());
    }

    public function testGetNormalizationType(): void
    {
        /** @var MockObject|RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        $mapping = $this->getCollectionMapping($router);

        static::assertSame($this->getNormalizationType(), $mapping->getNormalizationType());
    }

    public function testGetNormalizationFieldMappings(): void
    {
        /** @var MockObject|RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        $mapping = $this->getCollectionMapping($router);

        $fieldMappings = $mapping->getNormalizationFieldMappings('/');

        static::assertEquals($this->getNormalizationFieldMappings('/'), $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings(): void
    {
        /** @var MockObject|RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class);

        $mapping = $this->getCollectionMapping($router);

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        static::assertEquals([
            NormalizationFieldMappingBuilder::createEmbedMany('items')->getMapping(),
        ], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings(): void
    {
        /** @var MockObject|RouteParserInterface $router */
        $router = $this->getMockByCalls(RouteParserInterface::class, [
            Call::create('urlFor')
                ->with($this->getListRoute(), [], ['key' => 'value', 'offset' => 0, 'limit' => 20])
                ->willReturn(sprintf('%s?offset=0&limit=20', $this->getCollectionPath())),
            Call::create('urlFor')
                ->with($this->getCreateRoute(), [], [])
                ->willReturn($this->getCollectionPath()),
        ]);

        $mapping = $this->getCollectionMapping($router);

        $linkMappings = $mapping->getNormalizationLinkMappings('/');

        static::assertCount(2, $linkMappings);

        static::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[0]);
        static::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[1]);

        $object = new class() extends AbstractCollection {
        };

        $object->setOffset(0);
        $object->setLimit(20);
        $object->setCount(25);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getQueryParams')->with()->willReturn(['key' => 'value']),
        ]);

        /** @var MockObject|NormalizerContextInterface $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class, [
            Call::create('getRequest')->with()->willReturn($request),
        ]);

        $list = $linkMappings[0]->getLinkNormalizer()->normalizeLink('/', $object, $context);
        $create = $linkMappings[1]->getLinkNormalizer()->normalizeLink('/', $object, $context);

        static::assertSame([
            'href' => sprintf('%s?offset=0&limit=20', $this->getCollectionPath()),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $list);

        static::assertSame([
            'href' => $this->getCollectionPath(),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'POST',
            ],
        ], $create);
    }

    /**
     * @return NormalizationFieldMappingInterface[]
     */
    protected function getNormalizationFieldMappings(string $path): array
    {
        return [
            NormalizationFieldMappingBuilder::create('offset')->getMapping(),
            NormalizationFieldMappingBuilder::create('limit')->getMapping(),
            NormalizationFieldMappingBuilder::create('count')->getMapping(),
            NormalizationFieldMappingBuilder::create('filters')->getMapping(),
            NormalizationFieldMappingBuilder::create('sort')->getMapping(),
        ];
    }

    protected function getClass(): string
    {
        return CollectionInterface::class;
    }

    protected function getNormalizationType(): string
    {
        return 'collection';
    }

    protected function getListRoute(): string
    {
        return 'collection_list';
    }

    protected function getCreateRoute(): string
    {
        return 'collection_create';
    }

    protected function getCollectionPath(): string
    {
        return '/api/collection';
    }

    protected function getCollectionMapping(RouteParserInterface $router): AbstractCollectionMapping
    {
        return new class($router, $this->getClass(), $this->getNormalizationType(), $this->getListRoute(), $this->getCreateRoute()) extends AbstractCollectionMapping {
            public function __construct(
                RouteParserInterface $router,
                private string $class,
                private string $normalizationType,
                private string $listRouteName,
                private string $createRouteName
            ) {
                parent::__construct($router);
            }

            public function getClass(): string
            {
                return $this->class;
            }

            public function getNormalizationType(): string
            {
                return $this->normalizationType;
            }

            protected function getListRouteName(): string
            {
                return $this->listRouteName;
            }

            protected function getCreateRouteName(): string
            {
                return $this->createRouteName;
            }
        };
    }
}
