<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Parsing;

use App\Pet\Parsing\PetParsing;
use Chubbyphp\Parsing\ParserInterface;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

final class PetParsingFactory
{
    public function __invoke(ContainerInterface $container): PetParsing
    {
        /** @var ParserInterface $parser */
        $parser = $container->get(ParserInterface::class);

        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);

        return new PetParsing(
            $parser,
            $router,
        );
    }
}
