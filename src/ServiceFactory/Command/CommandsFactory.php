<?php

declare(strict_types=1);

namespace App\ServiceFactory\Command;

use Chubbyphp\CleanDirectories\ServiceFactory\CleanDirectoriesCommandFactory;
use Chubbyphp\Laminas\Config\Doctrine\ODM\MongoDB\Tools\Console\Command\DocumentManagerCommand;
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
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

final class CommandsFactory
{
    /**
     * @return array<int, Command>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [
            (new CleanDirectoriesCommandFactory())($container),
            new DocumentManagerCommand(new GenerateHydratorsCommand(), $container),
            new DocumentManagerCommand(new GeneratePersistentCollectionsCommand(), $container),
            new DocumentManagerCommand(new GenerateProxiesCommand(), $container),
            new DocumentManagerCommand(new QueryCommand(), $container),
            new DocumentManagerCommand(new MetadataCommand(), $container),
            new DocumentManagerCommand(new CreateCommand(), $container),
            new DocumentManagerCommand(new DropCommand(), $container),
            new DocumentManagerCommand(new ShardCommand(), $container),
            new DocumentManagerCommand(new UpdateCommand(), $container),
            new DocumentManagerCommand(new ValidateCommand(), $container),
        ];
    }
}
