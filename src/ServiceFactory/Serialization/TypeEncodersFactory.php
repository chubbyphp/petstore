<?php

declare(strict_types=1);

namespace App\ServiceFactory\Serialization;

use Chubbyphp\Serialization\Encoder\JsonTypeEncoder;
use Chubbyphp\Serialization\Encoder\JsonxTypeEncoder;
use Chubbyphp\Serialization\Encoder\TypeEncoderInterface;
use Chubbyphp\Serialization\Encoder\UrlEncodedTypeEncoder;
use Chubbyphp\Serialization\Encoder\YamlTypeEncoder;
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
