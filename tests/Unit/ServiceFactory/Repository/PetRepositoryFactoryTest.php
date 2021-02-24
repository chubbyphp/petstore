<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Repository;

use App\Repository\PetRepository;
use App\ServiceFactory\Repository\PetRepositoryFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
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
        /** @var DocumentManager $documentManager */
        $documentManager = $this->getMockByCalls(DocumentManager::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DocumentManager::class)->willReturn($documentManager),
        ]);

        $factory = new PetRepositoryFactory();

        self::assertInstanceOf(PetRepository::class, $factory($container));
    }
}
