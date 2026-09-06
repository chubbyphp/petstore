<?php

declare(strict_types=1);

namespace App\Tests\Unit\Pet\ServiceFactory\Parsing;

use App\Pet\Parsing\PetParsing;
use App\Pet\ServiceFactory\Parsing\PetParsingFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Parsing\ParserInterface;
use Mezzio\Helper\UrlHelperInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\Pet\ServiceFactory\Parsing\PetParsingFactory
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

        /** @var UrlHelperInterface $urlHelper */
        $urlHelper = $builder->create(UrlHelperInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ParserInterface::class], $parser),
            new WithReturn('get', [UrlHelperInterface::class], $urlHelper),
        ]);

        $factory = new PetParsingFactory();

        self::assertInstanceOf(PetParsing::class, $factory($container));
    }
}
