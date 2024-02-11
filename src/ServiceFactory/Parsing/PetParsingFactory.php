<?php

declare(strict_types=1);

namespace App\ServiceFactory\Parsing;

use App\Parsing\PetParsing;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Parsing\ParserInterface;
use Psr\Container\ContainerInterface;

final class PetParsingFactory
{
    public function __invoke(ContainerInterface $container): PetParsing
    {
        return new PetParsing(
            $container->get(ParserInterface::class),
            $container->get(UrlGeneratorInterface::class),
        );
    }
}
