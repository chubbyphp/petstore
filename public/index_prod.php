<?php

declare(strict_types=1);

/** @var Slim\App $app */

$env = 'prod';

$app = require __DIR__ . '/../app/app.php';
$app->run();
