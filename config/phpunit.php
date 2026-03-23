<?php

declare(strict_types=1);

$config = require_once __DIR__.'/dev.php';
$config['doctrine']['mongodbOdm']['configuration']['defaultDB'] .= '_phpunit';

return $config;
