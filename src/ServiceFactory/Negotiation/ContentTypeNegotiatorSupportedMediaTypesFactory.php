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
        /** @var DecoderInterface $decoder */
        $decoder = $container->get(DecoderInterface::class);

        return $decoder->getContentTypes();
    }
}
