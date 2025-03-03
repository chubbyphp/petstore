<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Parsing;

use App\Parsing\PetParsing;
use App\ServiceFactory\Parsing\PetParsingFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Parsing\ParserInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \App\ServiceFactory\Parsing\PetParsingFactory
 *
 * @internal
 */
final class PetParsingFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ParserInterface $parser */
        $parser = $builder->create(ParserInterface::class, []);

        /** @var RouteParserInterface $routeParser */
        $routeParser = $builder->create(RouteParserInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ParserInterface::class], $parser),
            new WithReturn('get', [RouteParserInterface::class], $routeParser),
        ]);

        $factory = new PetParsingFactory();

        self::assertInstanceOf(PetParsing::class, $factory($container));
    }
}
