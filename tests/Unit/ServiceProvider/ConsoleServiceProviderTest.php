<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceProvider;

use App\ServiceProvider\ConsoleServiceProvider;
use Chubbyphp\Config\Command\CleanDirectoriesCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\CreateDatabaseDoctrineCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\DropDatabaseDoctrineCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\CollectionRegionCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\EntityRegionCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\MetadataCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\QueryCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\QueryRegionCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ClearCache\ResultCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\EnsureProductionSettingsCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\InfoCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\RunDqlCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\SchemaTool\CreateCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\SchemaTool\DropCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\SchemaTool\UpdateCommand;
use Chubbyphp\DoctrineDbServiceProvider\Command\Orm\ValidateSchemaCommand;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \App\ServiceProvider\ConsoleServiceProvider
 *
 * @internal
 */
final class ConsoleServiceProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container([
            'chubbyphp.config.directories' => [
                'cache' => '/path/to/cache',
            ],
            'proxymanager.doctrine.dbal.connection_registry' => $this->getMockByCalls(ConnectionRegistry::class),
            'proxymanager.doctrine.orm.manager_registry' => $this->getMockByCalls(ManagerRegistry::class),
        ]);

        $serviceProvider = new ConsoleServiceProvider();
        $serviceProvider->register($container);

        self::assertArrayHasKey('console.commands', $container);

        $commands = $container['console.commands'];

        self::assertCount(16, $commands);

        self::assertInstanceOf(CleanDirectoriesCommand::class, $commands[0]);

        self::assertInstanceOf(CreateDatabaseDoctrineCommand::class, $commands[1]);
        self::assertInstanceOf(DropDatabaseDoctrineCommand::class, $commands[2]);

        self::assertInstanceOf(CollectionRegionCommand::class, $commands[3]);
        self::assertInstanceOf(EntityRegionCommand::class, $commands[4]);
        self::assertInstanceOf(MetadataCommand::class, $commands[5]);
        self::assertInstanceOf(QueryCommand::class, $commands[6]);
        self::assertInstanceOf(QueryRegionCommand::class, $commands[7]);
        self::assertInstanceOf(ResultCommand::class, $commands[8]);
        self::assertInstanceOf(EnsureProductionSettingsCommand::class, $commands[9]);
        self::assertInstanceOf(InfoCommand::class, $commands[10]);
        self::assertInstanceOf(RunDqlCommand::class, $commands[11]);
        self::assertInstanceOf(CreateCommand::class, $commands[12]);
        self::assertInstanceOf(DropCommand::class, $commands[13]);
        self::assertInstanceOf(UpdateCommand::class, $commands[14]);
        self::assertInstanceOf(ValidateSchemaCommand::class, $commands[15]);
    }
}
