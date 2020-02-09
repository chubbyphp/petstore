<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Mapping\Serialization\AbstractModelMapping;
use App\Model\ModelInterface;
use Chubbyphp\Framework\Router\RouterInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Mapping\NormalizationFieldMappingBuilder;
use Chubbyphp\Serialization\Mapping\NormalizationLinkMappingInterface;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapping\Serialization\AbstractModelMapping
 *
 * @internal
 */
class ModelMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        $mapping = $this->getModelMapping($router);

        self::assertSame($this->getClass(), $mapping->getClass());
    }

    public function testGetNormalizationType(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        $mapping = $this->getModelMapping($router);

        self::assertSame($this->getNormalizationType(), $mapping->getNormalizationType());
    }

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
        ], $fieldMappings);
    }

    public function testGetNormalizationEmbeddedFieldMappings(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        $mapping = $this->getModelMapping($router);

        $fieldMappings = $mapping->getNormalizationEmbeddedFieldMappings('/');

        self::assertEquals([], $fieldMappings);
    }

    public function testGetNormalizationLinkMappings(): void
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->getMockByCalls(RouterInterface::class, [
            Call::create('generatePath')
                ->with($this->getReadRoute(), ['id' => 'f183c7ff-7683-451e-807c-b916d9b5cf86'], [])
                ->willReturn(sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86')),
            Call::create('generatePath')
                ->with($this->getUpdateRoute(), ['id' => 'f183c7ff-7683-451e-807c-b916d9b5cf86'], [])
                ->willReturn(sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86')),
            Call::create('generatePath')
                ->with($this->getDeleteRoute(), ['id' => 'f183c7ff-7683-451e-807c-b916d9b5cf86'], [])
                ->willReturn(sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86')),
        ]);

        $mapping = $this->getModelMapping($router);

        $linkMappings = $mapping->getNormalizationLinkMappings('/');

        self::assertCount(3, $linkMappings);

        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[0]);
        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[1]);
        self::assertInstanceOf(NormalizationLinkMappingInterface::class, $linkMappings[2]);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class, [
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
            Call::create('getId')->with()->willReturn('f183c7ff-7683-451e-807c-b916d9b5cf86'),
        ]);

        /** @var NormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(NormalizerContextInterface::class);

        $read = $linkMappings[0]->getLinkNormalizer()->normalizeLink('/', $model, $context);
        $update = $linkMappings[1]->getLinkNormalizer()->normalizeLink('/', $model, $context);
        $delete = $linkMappings[2]->getLinkNormalizer()->normalizeLink('/', $model, $context);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'GET',
            ],
        ], $read);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'PUT',
            ],
        ], $update);

        self::assertSame([
            'href' => sprintf($this->getModelPath(), 'f183c7ff-7683-451e-807c-b916d9b5cf86'),
            'templated' => false,
            'rel' => [],
            'attributes' => [
                'method' => 'DELETE',
            ],
        ], $delete);
    }

    protected function getClass(): string
    {
        return ModelInterface::class;
    }

    protected function getNormalizationType(): string
    {
        return 'model';
    }

    protected function getReadRoute(): string
    {
        return 'model_read';
    }

    protected function getUpdateRoute(): string
    {
        return 'model_update';
    }

    protected function getDeleteRoute(): string
    {
        return 'model_delete';
    }

    protected function getModelPath(): string
    {
        return '/api/collection/%s';
    }

    protected function getModelMapping(RouterInterface $router): AbstractModelMapping
    {
        return new class($router, $this->getClass(), $this->getNormalizationType(), $this->getReadRoute(), $this->getUpdateRoute(), $this->getDeleteRoute()) extends AbstractModelMapping {
            /**
             * @var string
             */
            private $class;

            /**
             * @var string
             */
            private $normalizationType;

            /**
             * @var string
             */
            private $readRouteName;

            /**
             * @var string
             */
            private $updateRouteName;

            /**
             * @var string
             */
            private $deleteRouteName;

            public function __construct(
                RouterInterface $router,
                string $class,
                string $normalizationType,
                string $readRouteName,
                string $updateRouteName,
                string $deleteRouteName
            ) {
                parent::__construct($router);

                $this->class = $class;
                $this->normalizationType = $normalizationType;
                $this->readRouteName = $readRouteName;
                $this->updateRouteName = $updateRouteName;
                $this->deleteRouteName = $deleteRouteName;
            }

            public function getClass(): string
            {
                return $this->class;
            }

            public function getNormalizationType(): string
            {
                return $this->normalizationType;
            }

            protected function getReadRouteName(): string
            {
                return $this->readRouteName;
            }

            protected function getUpdateRouteName(): string
            {
                return $this->updateRouteName;
            }

            protected function getDeleteRouteName(): string
            {
                return $this->deleteRouteName;
            }
        };
    }
}
