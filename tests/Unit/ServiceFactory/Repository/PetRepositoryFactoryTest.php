<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Repository;

use App\Repository\PetRepository;
use App\ServiceFactory\Repository\PetRepositoryFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(EntityManager::class)->willReturn($entityManager),
        ]);

        $factory = new PetRepositoryFactory();

        self::assertInstanceOf(PetRepository::class, $factory($container));
    }
}
