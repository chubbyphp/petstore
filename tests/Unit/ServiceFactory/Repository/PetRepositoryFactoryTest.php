<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Repository;

use App\Repository\PetRepository;
use App\ServiceFactory\Repository\PetRepositoryFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Repository\PetRepositoryFactory
 *
 * @internal
 */
final class PetRepositoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var EntityManager $entityManager */
        $entityManager = $builder->create(EntityManager::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [EntityManager::class], $entityManager),
        ]);

        $factory = new PetRepositoryFactory();

        self::assertInstanceOf(PetRepository::class, $factory($container));
    }
}
