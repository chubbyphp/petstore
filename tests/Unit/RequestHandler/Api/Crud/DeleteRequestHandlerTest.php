<?php

declare(strict_types=1);

namespace App\Tests\Unit\RequestHandler\Api\Crud;

use App\Model\ModelInterface;
use App\Repository\RepositoryInterface;
use App\RequestHandler\Api\Crud\DeleteRequestHandler;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \App\RequestHandler\Api\Crud\DeleteRequestHandler
 *
 * @internal
 */
final class DeleteRequestHandlerTest extends TestCase
{
    public function testCreateResourceNotFoundInvalidUuid(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], '1234'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new DeleteRequestHandler($repository, $responseFactory);

        try {
            $requestHandler->handle($request);

            throw new \Exception('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testCreateResourceNotFoundMissingModel(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithReturn('findById', ['cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], null),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $requestHandler = new DeleteRequestHandler($repository, $responseFactory);

        try {
            $requestHandler->handle($request);

            throw new \Exception('Expected Exception');
        } catch (HttpExceptionInterface $e) {
            self::assertSame(404, $e->getStatus());
        }
    }

    public function testSuccessful(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getAttribute', ['id', null], 'cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'),
            new WithReturn('getAttribute', ['accept', null], 'application/json'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Content-Type', 'application/json']),
        ]);

        /** @var ModelInterface $model */
        $model = $builder->create(ModelInterface::class, []);

        /** @var RepositoryInterface $repository */
        $repository = $builder->create(RepositoryInterface::class, [
            new WithReturn('findById', ['cbb6bd79-b6a9-4b07-9d8b-f6be0f19aaa0'], $model),
            new WithReturnSelf('remove', [$model]),
            new WithReturnSelf('flush', []),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        $requestHandler = new DeleteRequestHandler($repository, $responseFactory);

        self::assertSame($response, $requestHandler->handle($request));
    }
}
