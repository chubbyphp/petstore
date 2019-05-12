<?php

declare(strict_types=1);

/** @var Slim\App $web */

$env = 'prod';

$web = require __DIR__ . '/../app/web.php';
$web->run();
