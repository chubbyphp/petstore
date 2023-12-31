<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Laminas\Config\Config;
use Chubbyphp\Laminas\Config\ContainerFactory;

return static function (string $env) {
    $config = require __DIR__.'/../config/'.$env.'.php';

    foreach ($config['directories'] ?? [] as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0o775, true);
        }
    }

    return (new ContainerFactory())(new Config($config));
};
