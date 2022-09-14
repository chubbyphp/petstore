<?php

declare(strict_types=1);

namespace App\ServiceFactory\Negotiation;

use Chubbyphp\DecodeEncode\Encoder\EncoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\EncoderFactory;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class AcceptNegotiatorSupportedMediaTypesFactory extends AbstractFactory
{
    /**
     * @return array<int, string>
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var EncoderInterface $encoder */
        $encoder = $this->resolveDependency($container, EncoderInterface::class, EncoderFactory::class);

        return $encoder->getContentTypes();
    }
}
