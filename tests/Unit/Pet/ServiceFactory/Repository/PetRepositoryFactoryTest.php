<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\Repository;

use App\Pet\Repository\PetRepository;
use App\Pet\ServiceFactory\Repository\PetRepositoryFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Pet\ServiceFactory\Repository\PetRepositoryFactory
 *
 * @internal
 */
final class PetRepositoryFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DocumentManager $documentManager */
        $documentManager = $builder->create(DocumentManager::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [DocumentManager::class], $documentManager),
        ]);

        $factory = new PetRepositoryFactory();

        self::assertInstanceOf(PetRepository::class, $factory($container));
    }
}
