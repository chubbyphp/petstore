<?php

declare(strict_types=1);

namespace App\ServiceFactory\Command;

use Chubbyphp\CleanDirectories\ServiceFactory\CleanDirectoriesCommandFactory;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\CreateCommand as DatabaseCreateCommand;
use Chubbyphp\Laminas\Config\Doctrine\DBAL\Tools\Console\Command\Database\DropCommand as DatabaseDropCommand;
use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;
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
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

final class CommandsFactory
{
    /**
     * @return array<int, Command>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var ConnectionProvider $connectionProvider */
        $connectionProvider = $container->get(ConnectionProvider::class);

        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $container->get(EntityManagerProvider::class);

        return [
            (new CleanDirectoriesCommandFactory())($container),
            new DatabaseCreateCommand($connectionProvider),
            new DatabaseDropCommand($connectionProvider),
            new ReservedWordsCommand($connectionProvider),
            new RunSqlCommand($connectionProvider),
            new CollectionRegionCommand($entityManagerProvider),
            new EntityRegionCommand($entityManagerProvider),
            new MetadataCommand($entityManagerProvider),
            new QueryCommand($entityManagerProvider),
            new QueryRegionCommand($entityManagerProvider),
            new ResultCommand($entityManagerProvider),
            new SchemaCreateCommand($entityManagerProvider),
            new SchemaDropCommand($entityManagerProvider),
            new SchemaUpdateCommand($entityManagerProvider),
            new ConvertMappingCommand($entityManagerProvider),
            new EnsureProductionSettingsCommand($entityManagerProvider),
            new GenerateProxiesCommand($entityManagerProvider),
            new InfoCommand($entityManagerProvider),
            new MappingDescribeCommand($entityManagerProvider),
            new RunDqlCommand($entityManagerProvider),
            new ValidateSchemaCommand($entityManagerProvider),
        ];
    }
}
