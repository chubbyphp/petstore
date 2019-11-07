#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App;

use App\ServiceProvider\ConsoleServiceProvider;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();

/** @var Container $container */
$container = (require __DIR__.'/container.php')($input->getParameterOption(['--env', '-e'], 'dev'));
$container->register(new ConsoleServiceProvider());

$console = new Application();
$console->getDefinition()->addOption(
    new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
);
$console->addCommands($container['console.commands']);
$console->run($input);
