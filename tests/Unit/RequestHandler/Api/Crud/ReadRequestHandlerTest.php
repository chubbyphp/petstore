<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\ReadRequestHandler;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\NotFound;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\ReadRequestHandler
 *
 * @internal
 */
final class ReadRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateResourceNotFoundInvalidUuid(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('1234'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(static function (NotFound $apiProblem): void {}),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        $requestHandler = new ReadRequestHandler($repository, $responseManager);

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testCreateResourceNotFoundMissingModel(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn(null),
        ]);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(static function (NotFound $apiProblem): void {}),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        $requestHandler = new ReadRequestHandler($repository, $responseManager);

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testSuccessful(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('id', null)->willReturn('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|ModelInterface $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var MockObject|RepositoryInterface $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('findById')->with('cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0')->willReturn($model),
        ]);

        /** @var MockObject|ResponseManagerInterface $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $model,
                    'application/json',
                    200,
                    new ArgumentCallback(static function (NormalizerContextInterface $context) use ($request): void {
                        self::assertSame($request, $context->getRequest());
                    })
                )
                ->willReturn($response),
        ]);

        $requestHandler = new ReadRequestHandler($repository, $responseManager);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
