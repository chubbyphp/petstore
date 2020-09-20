<?php

declare(strict_types=1);

$config = require __DIR__.'/dev.php';
$config['doctrine']['dbal']['connection']['dbname'] .= '_phpunit';

return $config;
