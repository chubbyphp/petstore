<?php

declare(strict_types=1);

namespace App\ServiceFactory\Negotiation;

use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Psr\Container\ContainerInterface;

final class AcceptNegotiatorSupportedMediaTypesFactory
{
    /**
     * @return array<int, string>
     */
    public function __invoke(ContainerInterface $container): array
    {
        return $container->get(EncoderInterface::class)->getContentTypes();
    }
}
