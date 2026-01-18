<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Parsing;

use App\Pet\Parsing\PetParsing;
use Chubbyphp\Parsing\ParserInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteParserInterface;

final class PetParsingFactory
{
    public function __invoke(ContainerInterface $container): PetParsing
    {
        /** @var ParserInterface $parser */
        $parser = $container->get(ParserInterface::class);

        /** @var RouteParserInterface $routeParser */
        $routeParser = $container->get(RouteParserInterface::class);

        return new PetParsing(
            $parser,
            $routeParser,
        );
    }
}
