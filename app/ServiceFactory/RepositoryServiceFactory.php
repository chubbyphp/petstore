<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Repository\PetRepository;
use Psr\Container\ContainerInterface;

final class RepositoryServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            PetRepository::class => static function (ContainerInterface $container) {
                return new PetRepository($container->get('doctrine.orm.em'));
            },
        ];
    }
}
