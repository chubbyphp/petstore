<?php

declare(strict_types=1);

namespace App\ServiceFactory\DecodeEncode;

use Chubbyphp\DecodeEncode\Encoder\JsonTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\JsonxTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\TypeEncoderInterface;
use Chubbyphp\DecodeEncode\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\DecodeEncode\Encoder\YamlTypeEncoder;
use Psr\Container\ContainerInterface;

final class TypeEncodersFactory
{
    /**
     * @return array<int, TypeEncoderInterface>
     */
    public function __invoke(ContainerInterface $container): array
    {
        $debug = $container->get('config')['debug'];

        return [
            new JsonTypeEncoder($debug),
            new JsonxTypeEncoder($debug),
            new UrlEncodedTypeEncoder(),
            new YamlTypeEncoder(),
        ];
    }
}
