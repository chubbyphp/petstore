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
use Slim\Http\Stream;

/**
 * @covers \App\Controller\Swagger\YamlController
 */
class YamlControllerTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Content-Type', 'application/x-yaml')->willReturnSelf(),
            Call::create('withHeader')
                ->with('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->willReturnSelf(),
            Call::create('withHeader')->with('Pragma', 'no-cache')->willReturnSelf(),
            Call::create('withHeader')->with('Expires', '0')->willReturnSelf(),
            Call::create('withBody')
                ->with(
                    new ArgumentCallback(function ($body) {
                        self::assertInstanceOf(Stream::class, $body);
                        self::assertRegExp('/^openapi: "3\.0\.0"/', (string) $body);
                    })
                )
                ->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(200, '')->willReturn($response),
        ]);

        $controller = new YamlController($responseFactory);

        self::assertSame($response, $controller($request));
    }
}
