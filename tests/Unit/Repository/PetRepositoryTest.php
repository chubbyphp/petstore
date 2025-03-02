<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;
use App\Model\ModelInterface;
use App\Model\Pet;
use App\Repository\PetRepository;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
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

        $collectionClass = $collection::class;

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            \sprintf(
                'App\Repository\PetRepository::resolveCollection() expects parameter 1 to be'
                    .' App\Collection\PetCollection, %s given',
                $collectionClass
            )
        );

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, []);

        $repository = new PetRepository($entityManager);
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
        $collection->setSort(['name' => 'asc']);

        $builder = new MockObjectBuilder();

        /** @var Comparison $likeNameFunc */
        $likeNameFunc = $builder->create(Comparison::class, []);

        /** @var Func $countIdFunc */
        $countIdFunc = $builder->create(Func::class, []);

        /** @var Expr $expr */
        $expr = $builder->create(Expr::class, [
            new WithReturn('like', ['p.name', ':name'], $likeNameFunc),
            new WithReturn('count', ['p.id'], $countIdFunc),
        ]);

        /** @var Query $countQuery */
        $countQuery = $builder->create(Query::class, [
            new WithReturn('getSingleScalarResult', [], (string) \count($items)),
        ]);

        /** @var Query $itemsQuery */
        $itemsQuery = $builder->create(Query::class, [
            new WithReturn('getResult', [AbstractQuery::HYDRATE_OBJECT], $items),
        ]);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $builder->create(QueryBuilder::class, [
            new WithReturn('expr', [], $expr),
            new WithReturnSelf('andWhere', [[$likeNameFunc]]),
            new WithReturnSelf('setParameter', ['name', '%sample%', null]),
            new WithReturnSelf('__clone', []),
            new WithReturn('expr', [], $expr),
            new WithReturnSelf('select', [[$countIdFunc]]),
            new WithReturn('getQuery', [], $countQuery),
            new WithReturnSelf('__clone', []),
            new WithReturnSelf('addOrderBy', ['p.name', 'asc']),
            new WithReturnSelf('setFirstResult', [0]),
            new WithReturnSelf('setMaxResults', [20]),
            new WithReturn('getQuery', [], $itemsQuery),
        ]);

        /** @var EntityRepository $repository */
        $repositoryMock = $builder->create(EntityRepository::class, [
            new WithReturn('createQueryBuilder', ['p', null], $queryBuilder),
        ]);

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, [
            new WithReturn('getRepository', [Pet::class], $repositoryMock),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->resolveCollection($collection);
    }

    public function testFindById(): void
    {
        $pet = new Pet();

        $builder = new MockObjectBuilder();

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, [
            new WithReturn('find', [Pet::class, '86c78085-edaf-4df9-95d0-563e45acf618', null, null], $pet),
        ]);

        $repository = new PetRepository($entityManager);

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
                'App\Repository\PetRepository::persist() expects parameter 1 to be App\Model\Pet, %s given',
                $modelClass
            )
        );

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, []);

        $repository = new PetRepository($entityManager);
        $repository->persist($model);
    }

    #[DoesNotPerformAssertions]
    public function testPersist(): void
    {
        $pet = new Pet();

        $builder = new MockObjectBuilder();

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, [
            new WithoutReturn('persist', [$pet]),
        ]);

        $repository = new PetRepository($entityManager);
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
                'App\Repository\PetRepository::remove() expects parameter 1 to be App\Model\Pet, %s given',
                $modelClass
            )
        );

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, []);

        $repository = new PetRepository($entityManager);
        $repository->remove($model);
    }

    #[DoesNotPerformAssertions]
    public function testRemove(): void
    {
        $pet = new Pet();

        $builder = new MockObjectBuilder();

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, [
            new WithoutReturn('remove', [$pet]),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->remove($pet);
    }

    #[DoesNotPerformAssertions]
    public function testFlush(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, [
            new WithoutReturn('flush', []),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->flush();
    }
}
