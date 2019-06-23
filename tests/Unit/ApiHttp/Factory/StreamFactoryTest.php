<?php

declare(strict_types=1);

namespace App\Tests\Unit\ApiHttp\Factory;

use App\ApiHttp\Factory\StreamFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\ApiHttp\Factory\StreamFactory
 */
class StreamFactoryTest extends TestCase
{
    public function testCreateStream(): void
    {
        $streamFactory = new StreamFactory();

        $stream = $streamFactory->createStream('content');

        self::assertSame('content', $stream->getContents());
    }

    public function testCreateStreamFromFileForMissingPath(): void
    {
        $filename = sys_get_temp_dir().'/'.uniqid().'-'.uniqid();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                '%s() could not create resource from file `%s`',
                StreamFactory::class.'::createStreamFromFile',
                $filename
            )
        );

        $streamFactory = new StreamFactory();
        $streamFactory->createStreamFromFile($filename);
    }

    public function testCreateStreamFromFile(): void
    {
        $filename = sys_get_temp_dir().'/'.uniqid().'-'.uniqid();

        file_put_contents($filename, 'content');

        $streamFactory = new StreamFactory();

        $stream = $streamFactory->createStreamFromFile($filename);

        self::assertSame('content', $stream->getContents());

        unlink($filename);
    }

    public function testCreateStreamFromResourceForInvalidResource(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Parameter 1 of %s() must be a resource.',
                StreamFactory::class.'::createStreamFromResource',
            )
        );

        $streamFactory = new StreamFactory();
        $streamFactory->createStreamFromResource('content');
    }

    public function testCreateStreamFromResource(): void
    {
        $filename = sys_get_temp_dir().'/'.uniqid().'-'.uniqid();

        file_put_contents($filename, 'content');

        $resource = fopen($filename, 'r');

        $streamFactory = new StreamFactory();

        $stream = $streamFactory->createStreamFromResource($resource);

        self::assertSame('content', $stream->getContents());

        unlink($filename);
    }
}
