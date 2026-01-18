<?php

declare(strict_types=1);

namespace App\Pet\ServiceFactory\Parsing;

use App\Pet\Parsing\PetParsing;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Parsing\ParserInterface;
use Psr\Container\ContainerInterface;

final class PetParsingFactory
{
    public function __invoke(ContainerInterface $container): PetParsing
    {
        /** @var ParserInterface $parser */
        $parser = $container->get(ParserInterface::class);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        return new PetParsing($parser, $urlGenerator);
    }
}
