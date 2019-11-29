<?php

declare(strict_types=1);

namespace App\ServiceFactory;

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
use Psr\Container\ContainerInterface;

final class ConsoleServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'console.commands' => static function (ContainerInterface $container) {
                $connectionRegistry = $container->get('proxymanager.doctrine.dbal.connection_registry');
                $managerRegistry = $container->get('proxymanager.doctrine.orm.manager_registry');

                return [
                    new CleanDirectoriesCommand($container->get('chubbyphp.config.directories')),

                    // doctrine dbal
                    new CreateDatabaseDoctrineCommand($connectionRegistry),
                    new DropDatabaseDoctrineCommand($connectionRegistry),

                    // doctrine orm
                    new CollectionRegionCommand($managerRegistry),
                    new EntityRegionCommand($managerRegistry),
                    new MetadataCommand($managerRegistry),
                    new QueryCommand($managerRegistry),
                    new QueryRegionCommand($managerRegistry),
                    new ResultCommand($managerRegistry),
                    new EnsureProductionSettingsCommand($managerRegistry),
                    new InfoCommand($managerRegistry),
                    new RunDqlCommand($managerRegistry),
                    new CreateCommand($managerRegistry),
                    new DropCommand($managerRegistry),
                    new UpdateCommand($managerRegistry),
                    new ValidateSchemaCommand($managerRegistry),
                ];
            },
        ];
    }
}
