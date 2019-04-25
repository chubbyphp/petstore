<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

/** @var Chubbyphp\Framework\Application $app */

$env = 'phpunit';

$app = require __DIR__ . '/../app/app.php';
$app->send($app->handle(ServerRequestFactory::fromGlobals()));
