<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Command;

use App\ServiceFactory\Command\CommandsFactory;
use App\Tests\Helper\AssertHelper;
use Chubbyphp\CleanDirectories\Command\CleanDirectoriesCommand;
use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GeneratePersistentCollectionsCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ShardCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ValidateCommand;
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

        self::assertCount(11, $commands);

        $cleanDirectoriesCommand = array_shift($commands);

        $generateHydratorsCommand = array_shift($commands);
        $generatePersistentCollectionsCommand = array_shift($commands);
        $generateProxiesCommand = array_shift($commands);
        $queryCommand = array_shift($commands);
        $metadataCommand = array_shift($commands);
        $createCommand = array_shift($commands);
        $dropCommand = array_shift($commands);
        $shardCommand = array_shift($commands);
        $updateCommand = array_shift($commands);
        $validateCommand = array_shift($commands);

        self::assertInstanceOf(CleanDirectoriesCommand::class, $cleanDirectoriesCommand);
        self::assertDocumentCommand(GenerateHydratorsCommand::class, $generateHydratorsCommand);
        self::assertDocumentCommand(GeneratePersistentCollectionsCommand::class, $generatePersistentCollectionsCommand);
        self::assertDocumentCommand(GenerateProxiesCommand::class, $generateProxiesCommand);
        self::assertDocumentCommand(QueryCommand::class, $queryCommand);
        self::assertDocumentCommand(MetadataCommand::class, $metadataCommand);
        self::assertDocumentCommand(CreateCommand::class, $createCommand);
        self::assertDocumentCommand(DropCommand::class, $dropCommand);
        self::assertDocumentCommand(ShardCommand::class, $shardCommand);
        self::assertDocumentCommand(UpdateCommand::class, $updateCommand);
        self::assertDocumentCommand(ValidateCommand::class, $validateCommand);
    }

    private static function assertDocumentCommand(
        string $expectedCommand,
        DocumentManagerCommand $entityManagerCommand
    ): void {
        self::assertInstanceOf($expectedCommand, AssertHelper::readProperty('command', $entityManagerCommand));
    }
}
