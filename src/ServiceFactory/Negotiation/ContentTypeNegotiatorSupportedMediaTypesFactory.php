<?php

declare(strict_types=1);

namespace App\ServiceFactory\Negotiation;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\ServiceFactory\DecoderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class ContentTypeNegotiatorSupportedMediaTypesFactory extends AbstractFactory
{
    /**
     * @return array<int, string>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->resolveDependency($container, DecoderInterface::class, DecoderFactory::class);

        return $decoder->getContentTypes();
    }
}
