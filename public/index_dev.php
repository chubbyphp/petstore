<?php

declare(strict_types=1);

use Zend\Diactoros\ServerRequestFactory;

/** @var Chubbyphp\Framework\Application $web */

$env = 'dev';

$web = require __DIR__ . '/../app/web.php';

$web->send($web->handle(ServerRequestFactory::fromGlobals()));
