<?php

declare(strict_types=1);

namespace Doctrine\ODM\MongoDB\Query {
    class Query
    {
        public const TYPE_FIND = 1;

        /** @return array|int */
        public function execute()
        {
        }
    }
}

namespace App\Tests\Unit\Repository {
    use App\Collection\CollectionInterface;
    use App\Collection\PetCollection;
    use App\Model\ModelInterface;
    use App\Model\Pet;
    use App\Repository\PetRepository;
    use Chubbyphp\Mock\Call;
    use Chubbyphp\Mock\MockByCallsTrait;
    use Doctrine\ODM\MongoDB\DocumentManager;
    use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
    use Doctrine\ODM\MongoDB\Query\Query;
    use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;

    /**
     * @covers \App\Repository\PetRepository
     *
     * @internal
     */
    final class PetRepositoryTest extends TestCase
    {
        use MockByCallsTrait;

        public function testResolveCollectionWithWrongCollection(): void
        {
            /** @var CollectionInterface|MockObject $collection */
            $collection = $this->getMockByCalls(CollectionInterface::class);

            $collectionClass = get_class($collection);

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                sprintf(
                    'App\Repository\PetRepository::resolveCollection() expects parameter 1 to be'
                        .' App\Collection\PetCollection, %s given',
                    $collectionClass
                )
            );

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class);

            $repository = new PetRepository($documentManager);
            $repository->resolveCollection($collection);
        }

        public function testResolveCollection(): void
        {
            $pet = new Pet();

            $items = [$pet];

            $collection = new PetCollection();
            $collection->setOffset(0);
            $collection->setLimit(20);
            $collection->setFilters(['name' => 'sample']);
            $collection->setSort(['name' => 'asc']);

            /** @var Query|MockObject $countQuery */
            $countQuery = $this->getMockByCalls(Query::class, [
                Call::create('execute')->with()->willReturn(count($items)),
            ]);

            /** @var Query|MockObject $itemsQuery */
            $itemsQuery = $this->getMockByCalls(Query::class, [
                Call::create('execute')->with()->willReturn(new class($items) {
                    private array $items;

                    public function __construct(array $items)
                    {
                        $this->items = $items;
                    }

                    public function toArray(): array
                    {
                        return $this->items;
                    }
                }),
            ]);

            /** @var QueryBuilder|MockObject $queryBuilder */
            $queryBuilder = $this->getMockByCalls(QueryBuilder::class, [
                Call::create('field')->with('name')->willReturnSelf(),
                Call::create('text')->with('sample')->willReturnSelf(),
                Call::create('count')->with()->willReturnSelf(),
                Call::create('getQuery')->with([])->willReturn($countQuery),
                Call::create('sort')->with('name', 'asc')->willReturnSelf(),
                Call::create('skip')->with(0)->willReturnSelf(),
                Call::create('limit')->with(20)->willReturnSelf(),
                Call::create('getQuery')->with([])->willReturn($itemsQuery),
            ]);

            /** @var DocumentRepository|MockObject $repository */
            $repository = $this->getMockByCalls(DocumentRepository::class, [
                Call::create('createQueryBuilder')->with()->willReturn($queryBuilder),
            ]);

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class, [
                Call::create('getRepository')->with(Pet::class)->willReturn($repository),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->resolveCollection($collection);
        }

        public function testFindById(): void
        {
            $pet = new Pet();

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class, [
                Call::create('find')
                    ->with(Pet::class, '86c78085-edaf-4df9-95d0-563e45acf618', 0, null)
                    ->willReturn($pet),
            ]);

            $repository = new PetRepository($documentManager);

            self::assertSame($pet, $repository->findById('86c78085-edaf-4df9-95d0-563e45acf618'));
        }

        public function testPersistWithWrongModel(): void
        {
            /** @var ModelInterface|MockObject $model */
            $model = $this->getMockByCalls(ModelInterface::class);

            $modelClass = get_class($model);

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                sprintf(
                    'App\Repository\PetRepository::persist() expects parameter 1 to be'
                        .' App\Model\Pet, %s given',
                    $modelClass
                )
            );

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class);

            $repository = new PetRepository($documentManager);
            $repository->persist($model);
        }

        public function testPersist(): void
        {
            $pet = new Pet();

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class, [
                Call::create('persist')->with($pet),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->persist($pet);
        }

        public function testRemoveWithWrongModel(): void
        {
            /** @var ModelInterface|MockObject $model */
            $model = $this->getMockByCalls(ModelInterface::class);

            $modelClass = get_class($model);

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                sprintf(
                    'App\Repository\PetRepository::remove() expects parameter 1 to be'
                        .' App\Model\Pet, %s given',
                    $modelClass
                )
            );

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class);

            $repository = new PetRepository($documentManager);
            $repository->remove($model);
        }

        public function testRemove(): void
        {
            $pet = new Pet();

            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class, [
                Call::create('remove')->with($pet),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->remove($pet);
        }

        public function testFlush(): void
        {
            /** @var DocumentManager|MockObject $documentManager */
            $documentManager = $this->getMockByCalls(DocumentManager::class, [
                Call::create('flush')->with([]),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->flush();
        }
    }
}
