<?php

declare(strict_types=1);

namespace App\ServiceFactory\Deserialization;

use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Psr\Container\ContainerInterface;

final class TypeDecodersFactory
{
    /**
     * @return array<int, TypeDecoderInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return [
            new JsonTypeDecoder(),
            new JsonxTypeDecoder(),
            new UrlEncodedTypeDecoder(),
            new YamlTypeDecoder(),
        ];
    }
}
