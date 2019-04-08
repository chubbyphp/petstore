<?php

declare(strict_types=1);

namespace App\Tests\Unit\Mapping\Serialization;

use App\Collection\PetCollection;
use App\Mapping\Serialization\AbstractCollectionMapping;
use App\Mapping\Serialization\PetCollectionMapping;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;

/**
 * @covers \App\Mapping\Serialization\PetCollectionMapping
 */
final class PetCollectionMappingTest extends CollectionMappingTest
{
    /**
     * @return string
     */
    protected function getClass(): string
    {
        return PetCollection::class;
    }

    /**
     * @return string
     */
    protected function getNormalizationType(): string
    {
        return 'petCollection';
    }

    /**
     * @return string
     */
    protected function getListRoute(): string
    {
        return 'pet_list';
    }

    /**
     * @return string
     */
    protected function getCreateRoute(): string
    {
        return 'pet_create';
    }

    /**
     * @return string
     */
    protected function getCollectionPath(): string
    {
        return '/api/pets';
    }

    /**
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return AbstractCollectionMapping
     */
    protected function getCollectionMapping(UrlGeneratorInterface $urlGenerator): AbstractCollectionMapping
    {
        return new PetCollectionMapping($urlGenerator);
    }
}
