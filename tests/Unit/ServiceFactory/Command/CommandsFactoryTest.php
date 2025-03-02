<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Command;

use App\ServiceFactory\Command\CommandsFactory;
use Chubbyphp\CleanDirectories\Command\CleanDirectoriesCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand as DatabaseDropCommand;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
use Doctrine\ORM\Tools\Console\Command\ClearCache\CollectionRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\InfoCommand;
use Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand as SchemaCreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand as SchemaDropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand as SchemaUpdateCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Command\CommandsFactory
 *
 * @internal
 */
final class CommandsFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $config = [
            'directories' => [],
        ];

        $builder = new MockObjectBuilder();

        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $builder->create(ConnectionProvider::class, []);

        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $builder->create(EntityManagerProvider::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ConnectionProvider::class], $connectionProvider),
            new WithReturn('get', [EntityManagerProvider::class], $entityManagerProvider),
            new WithReturn('get', ['config'], $config),
        ]);

        $factory = new CommandsFactory();

        $commands = $factory($container);

        self::assertIsArray($commands);

        $cleanDirectoriesCommand = array_shift($commands);
        $databaseCreateCommand = array_shift($commands);
        $databaseDropCommand = array_shift($commands);
        $runSqlCommand = array_shift($commands);
        $collectionRegionCommand = array_shift($commands);
        $entityRegionCommand = array_shift($commands);
        $metadataCommand = array_shift($commands);
        $queryCommand = array_shift($commands);
        $queryRegionCommand = array_shift($commands);
        $resultCommand = array_shift($commands);
        $schemaCreateCommand = array_shift($commands);
        $schemaDropCommand = array_shift($commands);
        $schemaUpdateCommand = array_shift($commands);
        $generateProxiesCommand = array_shift($commands);
        $infoCommand = array_shift($commands);
        $mappingDescribeCommand = array_shift($commands);
        $runDqlCommand = array_shift($commands);
        $validateSchemaCommand = array_shift($commands);

        self::assertCount(0, $commands);

        self::assertInstanceOf(CleanDirectoriesCommand::class, $cleanDirectoriesCommand);
        self::assertInstanceOf(DatabaseCreateCommand::class, $databaseCreateCommand);
        self::assertInstanceOf(DatabaseDropCommand::class, $databaseDropCommand);
        self::assertInstanceOf(RunSqlCommand::class, $runSqlCommand);
        self::assertInstanceOf(CollectionRegionCommand::class, $collectionRegionCommand);
        self::assertInstanceOf(EntityRegionCommand::class, $entityRegionCommand);
        self::assertInstanceOf(MetadataCommand::class, $metadataCommand);
        self::assertInstanceOf(QueryCommand::class, $queryCommand);
        self::assertInstanceOf(QueryRegionCommand::class, $queryRegionCommand);
        self::assertInstanceOf(ResultCommand::class, $resultCommand);
        self::assertInstanceOf(SchemaCreateCommand::class, $schemaCreateCommand);
        self::assertInstanceOf(SchemaDropCommand::class, $schemaDropCommand);
        self::assertInstanceOf(SchemaUpdateCommand::class, $schemaUpdateCommand);
        self::assertInstanceOf(GenerateProxiesCommand::class, $generateProxiesCommand);
        self::assertInstanceOf(InfoCommand::class, $infoCommand);
        self::assertInstanceOf(MappingDescribeCommand::class, $mappingDescribeCommand);
        self::assertInstanceOf(RunDqlCommand::class, $runDqlCommand);
        self::assertInstanceOf(ValidateSchemaCommand::class, $validateSchemaCommand);
    }
}
