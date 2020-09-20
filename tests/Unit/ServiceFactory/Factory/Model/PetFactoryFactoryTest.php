<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Factory\Model;

use App\Factory\Model\PetFactory;
use App\ServiceFactory\Factory\Model\PetFactoryFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Factory\Model\PetFactoryFactory
 *
 * @internal
 */
final class PetFactoryFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class);

        $factory = new PetFactoryFactory();

        self::assertInstanceOf(PetFactory::class, $factory($container));
    }
}
