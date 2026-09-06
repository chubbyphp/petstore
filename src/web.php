<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;
use Slim\App;

require_once __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require_once __DIR__.'/container.php')($env);

    /** @var App<ContainerInterface> $web */
    $web = $container->get(App::class);

    return $web;
};
