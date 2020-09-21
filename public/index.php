<?php

declare(strict_types=1);

/** @var Mezzio\Application $web */
$web = (require __DIR__ . '/../src/web.php')(getenv('APP_ENV'));
$web->run();
