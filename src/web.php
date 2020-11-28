<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

require __DIR__.'/../vendor/autoload.php';

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require __DIR__.'/container.php')($env);

    return new Application($container->get(MiddlewareInterface::class.'[]'));
};
