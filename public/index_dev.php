<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Slim\App $web */

$env = 'dev';

$web = require __DIR__ . '/../app/web.php';
$web->run((new ServerRequestFactory())->createFromGlobals());
