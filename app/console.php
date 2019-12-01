#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App;

use App\Config\DevConfig;
use App\Config\PhpunitConfig;
use App\Config\ProdConfig;
use App\ServiceFactory\ConsoleServiceFactory;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();

$env = $input->getParameterOption(['--env', '-e'], 'dev');

/** @var Container $container */
$container = (require __DIR__.'/container.php')();
$container->factories((new ConsoleServiceFactory())());

// always load this service provider last
// so that the values of other service providers can be overwritten.
$container->factories((new ConfigServiceFactory((new ConfigProvider([
    new DevConfig(__DIR__.'/..'),
    new PhpunitConfig(__DIR__.'/..'),
    new ProdConfig(__DIR__.'/..'),
]))->get($env)))());

$console = new Application();
$console->getDefinition()->addOption(
    new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
);
$console->addCommands($container->get('console.commands'));
$console->run($input);
