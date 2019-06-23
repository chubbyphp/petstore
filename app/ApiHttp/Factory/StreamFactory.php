<?php

declare(strict_types=1);

namespace App\ApiHttp\Factory;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream;

/**
 * @see https://github.com/slimphp/Slim-Psr7/blob/master/src/Factory/StreamFactory.php
 */
final class StreamFactory implements StreamFactoryInterface
{
    /**
     * @param string $content
     *
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $resource = fopen('php://temp', 'rw+');

        fwrite($resource, $content);
        rewind($resource);

        return $this->createStreamFromResource($resource);
    }

    /**
     * @param string $filename
     * @param string $mode
     *
     * @return StreamInterface
     *
     * @throws \RuntimeException
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $resource = @fopen($filename, $mode);

        if (!is_resource($resource)) {
            throw new \RuntimeException(
                sprintf('%s() could not create resource from file `%s`', __METHOD__, $filename)
            );
        }

        return $this->createStreamFromResource($resource);
    }

    /**
     * @param resource $resource
     *
     * @return StreamInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf('Parameter 1 of %s() must be a resource.', __METHOD__));
        }

        return new Stream($resource);
    }
}
