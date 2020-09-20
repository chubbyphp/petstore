<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

require __DIR__.'/../vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

return static function (string $env) {
    /** @var ContainerInterface $container */
    $container = (require __DIR__.'/container.php')($env);

    return new Application($container->get(MiddlewareInterface::class.'[]'));
};
