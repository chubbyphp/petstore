<?php

declare(strict_types=1);

namespace App\ServiceFactory\Command;

use App\Command\CleanDirectoriesCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand as DatabaseDropCommand;
use Chubbyphp\Laminas\Config\Doctrine\ORM\Tools\Console\Command\EntityManagerCommand;
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
            new CleanDirectoriesCommand($container->get('config')['directories']),
            new EntityManagerCommand(new DatabaseCreateCommand(), $container),
            new EntityManagerCommand(new DatabaseDropCommand(), $container),
            new EntityManagerCommand(new ReservedWordsCommand(), $container),
            new EntityManagerCommand(new RunSqlCommand(), $container),
            new EntityManagerCommand(new CollectionRegionCommand(), $container),
            new EntityManagerCommand(new EntityRegionCommand(), $container),
            new EntityManagerCommand(new MetadataCommand(), $container),
            new EntityManagerCommand(new QueryCommand(), $container),
            new EntityManagerCommand(new QueryRegionCommand(), $container),
            new EntityManagerCommand(new ResultCommand(), $container),
            new EntityManagerCommand(new SchemaCreateCommand(), $container),
            new EntityManagerCommand(new SchemaDropCommand(), $container),
            new EntityManagerCommand(new SchemaUpdateCommand(), $container),
            new EntityManagerCommand(new ConvertMappingCommand(), $container),
            new EntityManagerCommand(new EnsureProductionSettingsCommand(), $container),
            new EntityManagerCommand(new GenerateProxiesCommand(), $container),
            new EntityManagerCommand(new InfoCommand(), $container),
            new EntityManagerCommand(new MappingDescribeCommand(), $container),
            new EntityManagerCommand(new RunDqlCommand(), $container),
            new EntityManagerCommand(new ValidateSchemaCommand(), $container),
        ];
    }
}
