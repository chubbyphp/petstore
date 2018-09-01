<?php

declare(strict_types=1);

namespace App\Tests;

echo sprintf('php version: %s', phpversion()).PHP_EOL;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->setPsr4('App\Tests\\', __DIR__);
