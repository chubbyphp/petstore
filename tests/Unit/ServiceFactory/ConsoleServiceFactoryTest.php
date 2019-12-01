<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory;

use App\ServiceFactory\ConsoleServiceFactory;
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
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Persistence\ConnectionRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\ConsoleServiceFactory
 *
 * @internal
 */
final class ConsoleServiceFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testFactories(): void
    {
        $factories = (new ConsoleServiceFactory())();

        self::assertCount(1, $factories);
    }

    public function testConsoleCommands(): void
    {
        /** @var ConnectionRegistry|MockObject $connectionRegistry */
        $connectionRegistry = $this->getMockByCalls(ConnectionRegistry::class);

        /** @var ManagerRegistry|MockObject $managerRegistry */
        $managerRegistry = $this->getMockByCalls(ManagerRegistry::class);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('proxymanager.doctrine.dbal.connection_registry')->willReturn($connectionRegistry),
            Call::create('get')->with('proxymanager.doctrine.orm.manager_registry')->willReturn($managerRegistry),
            Call::create('get')->with('chubbyphp.config.directories')->willReturn([]),
        ]);

        $factories = (new ConsoleServiceFactory())();

        self::assertArrayHasKey('console.commands', $factories);

        $commands = $factories['console.commands']($container);

        self::assertIsArray($commands);

        self::assertCount(16, $commands);

        self::assertInstanceOf(CleanDirectoriesCommand::class, array_shift($commands));
        self::assertInstanceOf(CreateDatabaseDoctrineCommand::class, array_shift($commands));
        self::assertInstanceOf(DropDatabaseDoctrineCommand::class, array_shift($commands));
        self::assertInstanceOf(CollectionRegionCommand::class, array_shift($commands));
        self::assertInstanceOf(EntityRegionCommand::class, array_shift($commands));
        self::assertInstanceOf(MetadataCommand::class, array_shift($commands));
        self::assertInstanceOf(QueryCommand::class, array_shift($commands));
        self::assertInstanceOf(QueryRegionCommand::class, array_shift($commands));
        self::assertInstanceOf(ResultCommand::class, array_shift($commands));
        self::assertInstanceOf(EnsureProductionSettingsCommand::class, array_shift($commands));
        self::assertInstanceOf(InfoCommand::class, array_shift($commands));
        self::assertInstanceOf(RunDqlCommand::class, array_shift($commands));
        self::assertInstanceOf(CreateCommand::class, array_shift($commands));
        self::assertInstanceOf(DropCommand::class, array_shift($commands));
        self::assertInstanceOf(UpdateCommand::class, array_shift($commands));
        self::assertInstanceOf(ValidateSchemaCommand::class, array_shift($commands));
    }
}
