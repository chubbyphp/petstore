<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Slim\App $web */

$env = 'prod';

$web = require __DIR__ . '/../app/web.php';
$web->run((new ServerRequestFactory())->createFromGlobals());
