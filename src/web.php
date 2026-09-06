<?php

declare(strict_types=1);

namespace App;

use Mezzio\Application;
use Psr\Container\ContainerInterface;

require_once __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require_once __DIR__.'/container.php')($env);

    /** @var Application $web */
    $web = $container->get(Application::class);

    return $web;
};
