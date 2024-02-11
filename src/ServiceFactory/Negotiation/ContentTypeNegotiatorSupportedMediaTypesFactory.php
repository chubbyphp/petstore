<?php

declare(strict_types=1);

namespace App\ServiceFactory\Negotiation;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Psr\Container\ContainerInterface;

final class ContentTypeNegotiatorSupportedMediaTypesFactory
{
    /**
     * @return array<int, string>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return $container->get(DecoderInterface::class)->getContentTypes();
    }
}
