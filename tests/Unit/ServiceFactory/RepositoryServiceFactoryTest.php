<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\Repository\PetRepository;
use App\ServiceFactory\RepositoryServiceFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\RepositoryServiceFactory
 *
 * @internal
 */
final class RepositoryServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new RepositoryServiceFactory())();

        self::assertCount(1, $factories);
    }

    public function testResponseFactory(): void
    {
        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->getMockByCalls(EntityManager::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('doctrine.orm.em')->willReturn($entityManager),
        ]);

        $factories = (new RepositoryServiceFactory())();

        self::assertArrayHasKey(PetRepository::class, $factories);

        self::assertInstanceOf(
            PetRepository::class,
            $factories[PetRepository::class]($container)
        );
    }
}
