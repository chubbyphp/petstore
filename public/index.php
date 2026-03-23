<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Slim\App $web */
$web = (require_once __DIR__ . '/../src/web.php')(getenv('APP_ENV'));
$web->run((new ServerRequestFactory())->createFromGlobals());
