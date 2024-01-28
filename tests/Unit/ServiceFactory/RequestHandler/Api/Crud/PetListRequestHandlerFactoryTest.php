<?php

declare(strict_types=1);

namespace App\Tests\Unit\ServiceFactory\RequestHandler\Api\Crud;

use App\Parsing\ParsingInterface;
use App\Parsing\PetParsing;
use App\Repository\PetRepository;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ListRequestHandler;
use App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory;
use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \App\ServiceFactory\RequestHandler\Api\Crud\PetListRequestHandlerFactory
 *
 * @internal
 */
final class PetListRequestHandlerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ParsingInterface $petParsing */
        $petParsing = $this->getMockByCalls(ParsingInterface::class);

        /** @var RepositoryInterface $petRepository */
        $petRepository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var EncoderInterface $encoder */
        $encoder = $this->getMockByCalls(EncoderInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(PetParsing::class)->willReturn($petParsing),
            Call::create('get')->with(PetRepository::class)->willReturn($petRepository),
            Call::create('get')->with(EncoderInterface::class)->willReturn($encoder),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = new PetListRequestHandlerFactory();

        self::assertInstanceOf(ListRequestHandler::class, $factory($container));
    }
}
