<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Collection\CollectionInterface;
use App\Collection\PetCollection;
use App\Model\ModelInterface;
use App\Model\Pet;
use App\Repository\PetRepository;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
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

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class);

        $repository = new PetRepository($entityManager);
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

        /** @var Func|MockObject $likeNameFunc */
        $likeNameFunc = $this->getMockByCalls(Func::class);

        /** @var Func|MockObject $countIdFunc */
        $countIdFunc = $this->getMockByCalls(Func::class);

        /** @var Expr|MockObject $expr */
        $expr = $this->getMockByCalls(Expr::class, [
            Call::create('like')->with('p.name', ':name')->willReturn($likeNameFunc),
            Call::create('count')->with('p.id')->willReturn($countIdFunc),
        ]);

        /** @var AbstractQuery|MockObject $countQuery */
        $countQuery = $this->getMockByCalls(AbstractQuery::class, [
            Call::create('getSingleScalarResult')->with()->willReturn((string) count($items)),
        ]);

        /** @var AbstractQuery|MockObject $itemsQuery */
        $itemsQuery = $this->getMockByCalls(AbstractQuery::class, [
            Call::create('getResult')->with(AbstractQuery::HYDRATE_OBJECT)->willReturn($items),
        ]);

        /** @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockByCalls(QueryBuilder::class, [
            Call::create('expr')->with()->willReturn($expr),
            Call::create('andWhere')->with($likeNameFunc),
            Call::create('setParameter')->with('name', '%sample%', null),
            Call::create('expr')->with()->willReturn($expr),
            Call::create('select')->with($countIdFunc),
            Call::create('getQuery')->with()->willReturn($countQuery),
            Call::create('addOrderBy')->with('p.name', 'asc'),
            Call::create('setFirstResult')->with(0),
            Call::create('setMaxResults')->with(20),
            Call::create('getQuery')->with()->willReturn($itemsQuery),
        ]);

        /** @var EntityRepository|MockObject $repository */
        $repository = $this->getMockByCalls(EntityRepository::class, [
            Call::create('createQueryBuilder')->with('p', null)->willReturn($queryBuilder),
        ]);

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('getRepository')->with(Pet::class)->willReturn($repository),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->resolveCollection($collection);
    }

    public function testFindById(): void
    {
        $pet = new Pet();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('find')
                ->with(Pet::class, '86c78085-edaf-4df9-95d0-563e45acf618', null, null)
                ->willReturn($pet),
        ]);

        $repository = new PetRepository($entityManager);

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

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class);

        $repository = new PetRepository($entityManager);
        $repository->persist($model);
    }

    public function testPersist(): void
    {
        $pet = new Pet();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('persist')->with($pet),
        ]);

        $repository = new PetRepository($entityManager);
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

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class);

        $repository = new PetRepository($entityManager);
        $repository->remove($model);
    }

    public function testRemove(): void
    {
        $pet = new Pet();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('remove')->with($pet),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->remove($pet);
    }

    public function testFlush(): void
    {
        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('flush')->with(null),
        ]);

        $repository = new PetRepository($entityManager);
        $repository->flush();
    }
}
