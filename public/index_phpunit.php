<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

/** @var Chubbyphp\Framework\Application $app */

$env = 'phpunit';

$app = require __DIR__ . '/../app/app.php';

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

$app->send($app->handle($creator->fromGlobals()));
