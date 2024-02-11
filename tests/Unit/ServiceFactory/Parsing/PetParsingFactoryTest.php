<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Parsing;

use App\Parsing\PetParsing;
use App\ServiceFactory\Parsing\PetParsingFactory;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Parsing\ParserInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \App\ServiceFactory\Parsing\PetParsingFactory
 *
 * @internal
 */
final class PetParsingFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->getMockByCalls(ParserInterface::class);

        /** @var ParserInterface $urlGenerator */
        $urlGenerator = $this->getMockByCalls(UrlGeneratorInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ParserInterface::class)->willReturn($parser),
            Call::create('get')->with(UrlGeneratorInterface::class)->willReturn($urlGenerator),
        ]);

        $factory = new PetParsingFactory();

        self::assertInstanceOf(PetParsing::class, $factory($container));
    }
}
