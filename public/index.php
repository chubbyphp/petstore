<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Chubbyphp\Framework\Application $web */
$web = (require __DIR__ . '/../src/web.php')(getenv('APP_ENV'));
$web->emit($web->handle((new ServerRequestFactory())->createFromGlobals()));
