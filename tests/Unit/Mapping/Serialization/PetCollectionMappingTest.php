<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\PetCollection;
use App\Mapping\Serialization\AbstractCollectionMapping;
use App\Mapping\Serialization\PetCollectionMapping;
use Chubbyphp\Framework\Router\RouterInterface;

/**
 * @covers \App\Mapping\Serialization\PetCollectionMapping
 *
 * @internal
 */
final class PetCollectionMappingTest extends CollectionMappingTest
{
    protected function getClass(): string
    {
        return PetCollection::class;
    }

    protected function getNormalizationType(): string
    {
        return 'petCollection';
    }

    protected function getListRoute(): string
    {
        return 'pet_list';
    }

    protected function getCreateRoute(): string
    {
        return 'pet_create';
    }

    protected function getCollectionPath(): string
    {
        return '/api/pets';
    }

    protected function getCollectionMapping(RouterInterface $router): AbstractCollectionMapping
    {
        return new PetCollectionMapping($router);
    }
}
