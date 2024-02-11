<?php

declare(strict_types=1);

namespace App\Middleware;

use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\HttpException\HttpException;
use Chubbyphp\HttpException\HttpExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ApiExceptionMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private EncoderInterface $encoder,
        private ResponseFactoryInterface $responseFactory,
        private bool $debug = false,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            return $this->handleException($request, $exception);
        }
    }

    private function handleException(ServerRequestInterface $request, \Throwable $exception): ResponseInterface
    {
        $backtrace = $this->backtrace($exception);

        $httpException = $this->exceptionToHttpException($exception, $backtrace);

        $logLevel = $httpException->getStatus() >= 500 ? 'error' : 'info';

        $this->logger->{$logLevel}('Http Exception', ['backtrace' => $backtrace]);

        if (null === $accept = $request->getAttribute('accept')) {
            throw $exception;
        }

        $status = $httpException->getStatus();

        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', str_replace('/', '/problem+', $accept))
        ;

        $data = $httpException->jsonSerialize();
        $data['_type'] = 'apiProblem';

        $body = $this->encoder->encode($data, $accept);

        $response->getBody()->write($body);

        return $response;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function backtrace(\Throwable $exception): array
    {
        $exceptions = [];
        do {
            $exceptions[] = [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        } while ($exception = $exception->getPrevious());

        return $exceptions;
    }

    /**
     * @param array<int, array<string, mixed>> $backtrace
     */
    private function exceptionToHttpException(\Throwable $exception, array $backtrace): HttpExceptionInterface
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception;
        }

        if (!$this->debug) {
            return HttpException::createInternalServerError();
        }

        return HttpException::createInternalServerError([
            'detail' => $exception->getMessage(),
            'backtrace' => $backtrace,
        ]);
    }
}
