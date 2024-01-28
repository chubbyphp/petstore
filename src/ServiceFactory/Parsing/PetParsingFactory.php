<?php

declare(strict_types=1);

namespace App\ServiceFactory\Parsing;

use App\Parsing\PetParsing;
use Chubbyphp\Parsing\ParserInterface;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

final class PetParsingFactory
{
    public function __invoke(ContainerInterface $container): PetParsing
    {
        return new PetParsing(
            $container->get(ParserInterface::class),
            $container->get(RouterInterface::class),
        );
    }
}
