<?php

declare(strict_types=1);

namespace App\Tests\Unit\ApiHttp\Factory;

use PHPUnit\Framework\TestCase;
use App\ApiHttp\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \App\ApiHttp\Factory\ResponseFactory
 */
class ResponseFactoryTest extends TestCase
{
    public function testCreateResponse(): void
    {
        $factory = new ResponseFactory();

        $response = $factory->createResponse();

        self::assertInstanceOf(ResponseInterface::class, $response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('OK', $response->getReasonPhrase());
    }

    public function testCreateResponseWith307(): void
    {
        $factory = new ResponseFactory();

        $response = $factory->createResponse(307);

        self::assertInstanceOf(ResponseInterface::class, $response);

        self::assertSame(307, $response->getStatusCode());
        self::assertSame('Temporary Redirect', $response->getReasonPhrase());
    }

    public function testCreateResponseWith400AndReasonPhrase(): void
    {
        $factory = new ResponseFactory();

        $response = $factory->createResponse(400, 'NO IDEA');

        self::assertInstanceOf(ResponseInterface::class, $response);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('NO IDEA', $response->getReasonPhrase());
    }
}
