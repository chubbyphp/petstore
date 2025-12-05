<?php

declare(strict_types=1);

namespace Doctrine\ODM\MongoDB\Query {
    final class Query
    {
        public const TYPE_FIND = 1;

        public function execute(): mixed
        {
            return null;
        }
    }
}

namespace App\Tests\Unit\Repository {
    use App\Collection\CollectionInterface;
    use App\Collection\PetCollection;
    use App\Model\ModelInterface;
    use App\Model\Pet;
    use App\Repository\PetRepository;
    use Chubbyphp\Mock\MockMethod\WithoutReturn;
    use Chubbyphp\Mock\MockMethod\WithReturn;
    use Chubbyphp\Mock\MockMethod\WithReturnSelf;
    use Chubbyphp\Mock\MockObjectBuilder;
    use Doctrine\ODM\MongoDB\DocumentManager;
    use Doctrine\ODM\MongoDB\Iterator\IterableResult;
    use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
    use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
    use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
    use PHPUnit\Framework\TestCase;

    /**
     * @covers \App\Repository\PetRepository
     *
     * @internal
     */
    final class PetRepositoryTest extends TestCase
    {
        public function testResolveCollectionWithWrongCollection(): void
        {
            $builder = new MockObjectBuilder();

            /** @var CollectionInterface $collection */
            $collection = $builder->create(CollectionInterface::class, []);
            $collectionClass = \get_class($collection);

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                \sprintf(
                    'App\Repository\PetRepository::resolveCollection() expects parameter 1 to be'
                        .' App\Collection\PetCollection, %s given',
                    $collectionClass
                )
            );

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, []);

            $repository = new PetRepository($documentManager);
            $repository->resolveCollection($collection);
        }

        #[DoesNotPerformAssertions]
        public function testResolveCollection(): void
        {
            $pet = new Pet();
            $items = [$pet];

            $collection = new PetCollection();
            $collection->setOffset(0);
            $collection->setLimit(20);
            $collection->setFilters(['name' => 'sample']);
            $collection->setSort(['name' => 'asc', 'unknown' => null]);

            $builder = new MockObjectBuilder();

            /** @var IterableResult $countQuery */
            $countQuery = $builder->create(IterableResult::class, [
                new WithReturn('execute', [], \count($items)),
            ]);

            /** @var IterableResult $itemsQuery */
            $itemsQuery = $builder->create(IterableResult::class, [
                new WithReturn('execute', [], new class($items) {
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

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $builder->create(QueryBuilder::class, [
                new WithReturnSelf('field', ['name']),
                new WithReturnSelf('text', ['sample']),
                new WithReturnSelf('__clone', []),
                new WithReturnSelf('count', []),
                new WithReturn('getQuery', [[]], $countQuery),
                new WithReturnSelf('__clone', []),
                new WithReturnSelf('sort', ['name', 'asc']),
                new WithReturnSelf('skip', [0]),
                new WithReturnSelf('limit', [20]),
                new WithReturn('getQuery', [[]], $itemsQuery),
            ]);

            /** @var DocumentRepository $documentRepository */
            $documentRepository = $builder->create(DocumentRepository::class, [
                new WithReturn('createQueryBuilder', [], $queryBuilder),
            ]);

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, [
                new WithReturn('getRepository', [Pet::class], $documentRepository),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->resolveCollection($collection);
        }

        public function testFindById(): void
        {
            $pet = new Pet();

            $builder = new MockObjectBuilder();

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, [
                new WithReturn(
                    'find',
                    [Pet::class, '86c78085-edaf-4df9-95d0-563e45acf618', 0, null],
                    $pet
                ),
            ]);

            $repository = new PetRepository($documentManager);
            self::assertSame($pet, $repository->findById('86c78085-edaf-4df9-95d0-563e45acf618'));
        }

        public function testPersistWithWrongModel(): void
        {
            $builder = new MockObjectBuilder();

            /** @var ModelInterface $model */
            $model = $builder->create(ModelInterface::class, []);

            $modelClass = $model::class;

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                \sprintf(
                    'App\Repository\PetRepository::persist() expects parameter 1 to be'
                        .' App\Model\Pet, %s given',
                    $modelClass
                )
            );

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, []);

            $repository = new PetRepository($documentManager);
            $repository->persist($model);
        }

        #[DoesNotPerformAssertions]
        public function testPersist(): void
        {
            $pet = new Pet();

            $builder = new MockObjectBuilder();

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, [
                new WithoutReturn('persist', [$pet]),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->persist($pet);
        }

        public function testRemoveWithWrongModel(): void
        {
            $builder = new MockObjectBuilder();

            /** @var ModelInterface $model */
            $model = $builder->create(ModelInterface::class, []);
            $modelClass = $model::class;

            $this->expectException(\TypeError::class);
            $this->expectExceptionMessage(
                \sprintf(
                    'App\Repository\PetRepository::remove() expects parameter 1 to be'
                        .' App\Model\Pet, %s given',
                    $modelClass
                )
            );

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, []);

            $repository = new PetRepository($documentManager);
            $repository->remove($model);
        }

        #[DoesNotPerformAssertions]
        public function testRemove(): void
        {
            $pet = new Pet();

            $builder = new MockObjectBuilder();

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, [
                new WithoutReturn('remove', [$pet]),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->remove($pet);
        }

        #[DoesNotPerformAssertions]
        public function testFlush(): void
        {
            $builder = new MockObjectBuilder();

            /** @var DocumentManager $documentManager */
            $documentManager = $builder->create(DocumentManager::class, [
                new WithoutReturn('flush', [[]]),
            ]);

            $repository = new PetRepository($documentManager);
            $repository->flush();
        }
    }
}
