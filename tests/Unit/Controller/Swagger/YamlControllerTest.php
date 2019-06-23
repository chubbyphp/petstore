<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Swagger;

use App\Controller\Swagger\YamlController;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \App\Controller\Swagger\YamlController
 */
class YamlControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var StreamInterface|MockObject $responseStream */
        $responseStream = $this->getMockByCalls(StreamInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/x-yaml')->willReturnSelf(),
            Call::create('withHeader')
                ->with('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->willReturnSelf(),
            Call::create('withHeader')->with('Pragma', 'no-cache')->willReturnSelf(),
            Call::create('withHeader')->with('Expires', '0')->willReturnSelf(),
            Call::create('withBody')->with($responseStream)->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        /** @var StreamFactoryInterface|MockObject $streamFactory */
        $streamFactory = $this->getMockByCalls(StreamFactoryInterface::class, [
            Call::create('createStreamFromFile')
                ->with(
                    new ArgumentCallback(function (string $path) {
                        self::assertRegExp('#swagger/swagger\.yml$#', $path);
                    }),
                    'r'
                )
                ->willReturn($responseStream),
        ]);

        $controller = new YamlController($responseFactory, $streamFactory);

        self::assertSame($response, $controller->handle($request));
    }
}
