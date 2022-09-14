<?php

declare(strict_types=1);

namespace App\ServiceFactory\Deserialization;

use Chubbyphp\DecodeEncode\Decoder\JsonTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\JsonxTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\TypeDecoderInterface;
use Chubbyphp\DecodeEncode\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\YamlTypeDecoder;

final class TypeDecodersFactory
{
    /**
     * @return array<int, TypeDecoderInterface>
     */
    public function __invoke(): array
    {
        return [
            new JsonTypeDecoder(),
            new JsonxTypeDecoder(),
            new UrlEncodedTypeDecoder(),
            new YamlTypeDecoder(),
        ];
    }
}
