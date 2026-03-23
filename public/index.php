<?php

declare(strict_types=1);

/** @var Mezzio\Application $web */
$web = (require_once __DIR__ . '/../src/web.php')(getenv('APP_ENV'));
$web->run();
