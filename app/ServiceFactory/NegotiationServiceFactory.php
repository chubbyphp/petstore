<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Psr\Container\ContainerInterface;

final class NegotiationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'negotiator.acceptNegotiator.values' => static function (ContainerInterface $container) {
                return $container->get('serializer')->getContentTypes();
            },
            'negotiator.contentTypeNegotiator.values' => static function (ContainerInterface $container) {
                return $container->get('deserializer')->getContentTypes();
            },
        ];
    }
}
