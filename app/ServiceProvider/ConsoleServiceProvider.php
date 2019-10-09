<?php

declare(strict_types=1);

namespace App\ServiceProvider;

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
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['console.commands'] = static function () use ($container) {
            return [
                new CleanDirectoriesCommand($container['chubbyphp.config.directories']),

                // doctrine dbal
                new CreateDatabaseDoctrineCommand($container['proxymanager.doctrine.dbal.connection_registry']),
                new DropDatabaseDoctrineCommand($container['proxymanager.doctrine.dbal.connection_registry']),

                // doctrine orm
                new CollectionRegionCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new EntityRegionCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new MetadataCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new QueryCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new QueryRegionCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new ResultCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new EnsureProductionSettingsCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new InfoCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new RunDqlCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new CreateCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new DropCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new UpdateCommand($container['proxymanager.doctrine.orm.manager_registry']),
                new ValidateSchemaCommand($container['proxymanager.doctrine.orm.manager_registry']),
            ];
        };
    }
}
