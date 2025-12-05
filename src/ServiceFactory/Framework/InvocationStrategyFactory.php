<?php

declare(strict_types=1);

namespace App\ServiceFactory\Framework;

use Slim\Handlers\Strategies\RequestHandler;
use Slim\Interfaces\InvocationStrategyInterface;

final class InvocationStrategyFactory
{
    public function __invoke(): InvocationStrategyInterface
    {
        return new RequestHandler(true);
    }
}
