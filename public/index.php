<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Chubbyphp\Framework\Application $web */
$web = (require __DIR__ . '/../app/web.php')($_ENV['APP_ENV']);
$web->send($web->handle((new ServerRequestFactory())->createFromGlobals()));
