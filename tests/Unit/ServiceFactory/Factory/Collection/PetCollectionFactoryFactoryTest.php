<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Factory\Collection;

use App\Factory\Collection\PetCollectionFactory;
use App\ServiceFactory\Factory\Collection\PetCollectionFactoryFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Factory\Collection\PetCollectionFactoryFactory
 *
 * @internal
 */
final class PetCollectionFactoryFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new PetCollectionFactoryFactory();

        self::assertInstanceOf(PetCollectionFactory::class, $factory($container));
    }
}
