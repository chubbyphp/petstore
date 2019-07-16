<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Crud;

use App\ApiHttp\Factory\InvalidParametersFactoryInterface;
use App\Collection\CollectionInterface;
use App\Factory\CollectionFactoryInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Crud\ListRequestHandler;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\BadRequest;
use Chubbyphp\ApiHttp\Error\ErrorInterface;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\Crud\ListRequestHandler
 *
 * @internal
 */
class ListRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateWithValidationError(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class);

        $invalidParameters = [
            ['name' => 'offset', 'reason' => 'notinteger', 'details' => []],
        ];

        /** @var InvalidParametersFactoryInterface|MockObject $invalidParametersFactory */
        $invalidParametersFactory = $this->getMockByCalls(InvalidParametersFactoryInterface::class, [
            Call::create('createInvalidParameters')
                ->with([$error])
                ->willReturn($invalidParameters),
        ]);

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class);

        /** @var CollectionFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(CollectionFactoryInterface::class, [
            Call::create('create')->with()->willReturn($collection),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestQuery')
                ->with($request, $collection, null)
                ->willReturn($collection),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(function (BadRequest $apiProblem) use ($invalidParameters): void {
                        self::assertSame($invalidParameters, $apiProblem->getInvalidParameters());
                    }),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($collection, null, '')->willReturn([$error]),
        ]);

        $requestHandler = new ListRequestHandler(
            $invalidParametersFactory,
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testSuccessful(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var InvalidParametersFactoryInterface|MockObject $invalidParametersFactory */
        $invalidParametersFactory = $this->getMockByCalls(InvalidParametersFactoryInterface::class);

        /** @var CollectionInterface|MockObject $collection */
        $collection = $this->getMockByCalls(CollectionInterface::class);

        /** @var CollectionFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(CollectionFactoryInterface::class, [
            Call::create('create')->with()->willReturn($collection),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('resolveCollection')->with($collection),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestQuery')
                ->with($request, $collection, null)
                ->willReturn($collection),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $collection,
                    'application/json',
                    200,
                    new ArgumentCallback(function (NormalizerContextInterface $context) use ($request): void {
                        self::assertSame($request, $context->getRequest());
                    })
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($collection, null, '')->willReturn([]),
        ]);

        $requestHandler = new ListRequestHandler(
            $invalidParametersFactory,
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
