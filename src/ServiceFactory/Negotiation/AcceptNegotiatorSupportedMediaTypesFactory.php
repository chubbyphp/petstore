<?php

declare(strict_types=1);

namespace App\ServiceFactory\Negotiation;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Chubbyphp\Serialization\Encoder\EncoderInterface;
use Chubbyphp\Serialization\ServiceFactory\EncoderFactory;
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
