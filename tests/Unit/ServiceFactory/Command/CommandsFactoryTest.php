<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Command;

use App\Command\CleanDirectoriesCommand;
use App\ServiceFactory\Command\CommandsFactory;
use App\Tests\Helper\AssertHelper;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand as DatabaseDropCommand;
use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\CollectionRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryRegionCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand;
use Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\InfoCommand;
use Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand as SchemaCreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand as SchemaDropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand as SchemaUpdateCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Command\CommandsFactory
 *
 * @internal
 */
final class CommandsFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn(['directories' => []]),
        ]);

        $factory = new CommandsFactory();

        $commands = $factory($container);

        self::assertIsArray($commands);

        self::assertCount(21, $commands);

        $cleanDirectoriesCommand = array_shift($commands);
        $databaseCreateCommand = array_shift($commands);
        $databaseDropCommand = array_shift($commands);
        $reservedWordsCommand = array_shift($commands);
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
        $convertMappingCommand = array_shift($commands);
        $ensureProductionSettingsCommand = array_shift($commands);
        $generateProxiesCommand = array_shift($commands);
        $infoCommand = array_shift($commands);
        $mappingDescribeCommand = array_shift($commands);
        $runDqlCommand = array_shift($commands);
        $validateSchemaCommand = array_shift($commands);

        self::assertInstanceOf(CleanDirectoriesCommand::class, $cleanDirectoriesCommand);
        self::assertEntityCommand(DatabaseCreateCommand::class, $databaseCreateCommand);
        self::assertEntityCommand(DatabaseDropCommand::class, $databaseDropCommand);
        self::assertEntityCommand(ReservedWordsCommand::class, $reservedWordsCommand);
        self::assertEntityCommand(RunSqlCommand::class, $runSqlCommand);
        self::assertEntityCommand(CollectionRegionCommand::class, $collectionRegionCommand);
        self::assertEntityCommand(EntityRegionCommand::class, $entityRegionCommand);
        self::assertEntityCommand(MetadataCommand::class, $metadataCommand);
        self::assertEntityCommand(QueryCommand::class, $queryCommand);
        self::assertEntityCommand(QueryRegionCommand::class, $queryRegionCommand);
        self::assertEntityCommand(ResultCommand::class, $resultCommand);
        self::assertEntityCommand(SchemaCreateCommand::class, $schemaCreateCommand);
        self::assertEntityCommand(SchemaDropCommand::class, $schemaDropCommand);
        self::assertEntityCommand(SchemaUpdateCommand::class, $schemaUpdateCommand);
        self::assertEntityCommand(ConvertMappingCommand::class, $convertMappingCommand);
        self::assertEntityCommand(EnsureProductionSettingsCommand::class, $ensureProductionSettingsCommand);
        self::assertEntityCommand(GenerateProxiesCommand::class, $generateProxiesCommand);
        self::assertEntityCommand(InfoCommand::class, $infoCommand);
        self::assertEntityCommand(MappingDescribeCommand::class, $mappingDescribeCommand);
        self::assertEntityCommand(RunDqlCommand::class, $runDqlCommand);
        self::assertEntityCommand(ValidateSchemaCommand::class, $validateSchemaCommand);
    }

    private static function assertEntityCommand(
        string $expectedCommand,
        EntityManagerCommand $entityManagerCommand
    ): void {
        self::assertInstanceOf($expectedCommand, AssertHelper::readProperty('command', $entityManagerCommand));
    }
}
