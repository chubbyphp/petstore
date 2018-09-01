<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Collection\CollectionInterface;
use App\Model\ModelInterface;
use App\Repository\AbstractRepository;
use App\Repository\RepositoryInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repository\AbstractRepository
 */
class RepositoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testResolveCollection()
    {
        $model = $this->getModel();
        $modelClass = get_class($model);

        $items = [$model];

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class, [
            Call::create('getOffset')->with()->willReturn(0),
            Call::create('getLimit')->with()->willReturn(20),
            Call::create('setItems')->with($items),
        ]);

        /** @var AbstractQuery|MockObject $query */
        $query = $this->getMockByCalls(AbstractQuery::class, [
            Call::create('getResult')->with(AbstractQuery::HYDRATE_OBJECT)->willReturn($items),
        ]);

        /** @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockByCalls(QueryBuilder::class, [
            Call::create('setFirstResult')->with(0),
            Call::create('setMaxResults')->with(20),
            Call::create('getQuery')->with()->willReturn($query),
        ]);

        /** @var EntityRepository|MockObject $repository */
        $repository = $this->getMockByCalls(EntityRepository::class, [
            Call::create('createQueryBuilder')->with('p', null)->willReturn($queryBuilder),
        ]);

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('getRepository')->with($modelClass)->willReturn($repository),
        ]);

        $repository = $this->getRepository($entityManager);

        $repository->resolveCollection($collection);
    }

    public function testFindById()
    {
        $model = $this->getModel();
        $modelClass = get_class($model);

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('find')
                ->with($modelClass, '86c78085-edaf-4df9-95d0-563e45acf618', null, null)
                ->willReturn($model),
        ]);

        $repository = $this->getRepository($entityManager);

        self::assertSame($model, $repository->findById('86c78085-edaf-4df9-95d0-563e45acf618'));
    }

    public function testPersist()
    {
        $model = $this->getModel();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('persist')->with($model),
        ]);

        $repository = $this->getRepository($entityManager);

        $repository->persist($model);
    }

    public function testRemove()
    {
        $model = $this->getModel();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('remove')->with($model),
        ]);

        $repository = $this->getRepository($entityManager);

        $repository->remove($model);
    }

    public function testFlush()
    {
        $model = $this->getModel();

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class, [
            Call::create('flush')->with(null),
        ]);

        $repository = $this->getRepository($entityManager);

        $repository->flush();
    }

    /**
     * @return ModelInterface
     */
    protected function getModel(): ModelInterface
    {
        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        return $model;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return RepositoryInterface
     */
    protected function getRepository(EntityManager $entityManager): RepositoryInterface
    {
        $model = $this->getModel();
        $modelClass = get_class($model);

        return new class($entityManager, $modelClass) extends AbstractRepository {
            /**
             * @var string
             */
            private $modelClass;

            /**
             * @param EntityManager $entityManager
             * @param string        $modelClass
             */
            public function __construct(EntityManager $entityManager, string $modelClass)
            {
                parent::__construct($entityManager);

                $this->modelClass = $modelClass;
            }

            /**
             * @return string
             */
            protected function getModelClass(): string
            {
                return $this->modelClass;
            }
        };
    }
}
