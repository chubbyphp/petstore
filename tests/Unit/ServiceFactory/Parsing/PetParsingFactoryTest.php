<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\Parsing;

use App\Parsing\PetParsing;
use App\ServiceFactory\Parsing\PetParsingFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Parsing\ParserInterface;
use Mezzio\Router\RouterInterface;
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

        /** @var RouterInterface $router */
        $router = $this->getMockByCalls(RouterInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ParserInterface::class)->willReturn($parser),
            Call::create('get')->with(RouterInterface::class)->willReturn($router),
        ]);

        $factory = new PetParsingFactory();

        self::assertInstanceOf(PetParsing::class, $factory($container));
    }
}
