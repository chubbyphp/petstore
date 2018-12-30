<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\Model\Pet;
use App\Repository\Repository;
use App\ServiceProvider\RepositoryServiceProvider;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\RepositoryServiceProvider
 */
final class RepositoryServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'doctrine.orm.em' => $this->getMockByCalls(EntityManager::class),
        ]);

        $serviceProvider = new RepositoryServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey(Repository::class.Pet::class, $container);

        self::assertInstanceOf(Repository::class, $container[Repository::class.Pet::class]);
    }
}
