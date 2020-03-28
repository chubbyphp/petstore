<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Factory\ModelFactoryInterface;
use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\CreateRequestHandler;
use Chubbyphp\ApiHttp\ApiProblem\ClientError\UnprocessableEntity;
use Chubbyphp\ApiHttp\Manager\RequestManagerInterface;
use Chubbyphp\ApiHttp\Manager\ResponseManagerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Serialization\Normalizer\NormalizerContextInterface;
use Chubbyphp\Validation\Error\ErrorInterface;
use Chubbyphp\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\CreateRequestHandler
 *
 * @internal
 */
final class CreateRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreateWithValidationError(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('accept', null)->willReturn('application/json'),
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ErrorInterface|MockObject $error */
        $error = $this->getMockByCalls(ErrorInterface::class, [
            Call::create('getPath')->with()->willReturn('name'),
            Call::create('getKey')->with()->willReturn('notunique'),
            Call::create('getArguments')->with()->willReturn([]),
        ]);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var ModelFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(ModelFactoryInterface::class, [
            Call::create('create')->with()->willReturn($model),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(function (DenormalizerContextInterface $context): void {
                        self::assertTrue($context->isClearMissing());
                    })
                )
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('createFromApiProblem')
                ->with(
                    new ArgumentCallback(function (UnprocessableEntity $apiProblem): void {
                        self::assertSame(
                            [['name' => 'name', 'reason' => 'notunique', 'details' => []]],
                            $apiProblem->getInvalidParameters()
                        );
                    }),
                    'application/json',
                    null
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([$error]),
        ]);

        $requestHandler = new CreateRequestHandler(
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
            Call::create('getAttribute')->with('contentType', null)->willReturn('application/json'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ModelInterface|MockObject $model */
        $model = $this->getMockByCalls(ModelInterface::class);

        /** @var ModelFactoryInterface|MockObject $factory */
        $factory = $this->getMockByCalls(ModelFactoryInterface::class, [
            Call::create('create')->with()->willReturn($model),
        ]);

        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMockByCalls(RepositoryInterface::class, [
            Call::create('persist')->with($model),
            Call::create('flush')->with(),
        ]);

        /** @var RequestManagerInterface|MockObject $requestManager */
        $requestManager = $this->getMockByCalls(RequestManagerInterface::class, [
            Call::create('getDataFromRequestBody')
                ->with(
                    $request,
                    $model,
                    'application/json',
                    new ArgumentCallback(function (DenormalizerContextInterface $context): void {
                        self::assertTrue($context->isClearMissing());
                    })
                )
                ->willReturn($model),
        ]);

        /** @var ResponseManagerInterface|MockObject $responseManager */
        $responseManager = $this->getMockByCalls(ResponseManagerInterface::class, [
            Call::create('create')
                ->with(
                    $model,
                    'application/json',
                    201,
                    new ArgumentCallback(function (NormalizerContextInterface $context) use ($request): void {
                        self::assertSame($request, $context->getRequest());
                    })
                )
                ->willReturn($response),
        ]);

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMockByCalls(ValidatorInterface::class, [
            Call::create('validate')->with($model, null, '')->willReturn([]),
        ]);

        $requestHandler = new CreateRequestHandler(
            $factory,
            $repository,
            $requestManager,
            $responseManager,
            $validator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
