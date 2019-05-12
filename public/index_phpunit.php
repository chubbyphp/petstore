<?php

declare(strict_types=1);

/** @var Slim\App $web */

$env = 'phpunit';

$web = require __DIR__ . '/../app/web.php';
$web->run();
