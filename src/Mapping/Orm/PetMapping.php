<?php

declare(strict_types=1);

namespace App\Mapping\Orm;

use App\Model\Vaccination;
use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class PetMapping implements ClassMapMappingInterface
{
    /**
     * @param ORMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('pet');
        $builder->createField('id', 'guid')->makePrimaryKey()->build();
        $builder->addField('createdAt', 'datetime');
        $builder->addField('updatedAt', 'datetime', ['nullable' => true]);
        $builder->addField('name', 'string');
        $builder->addField('tag', 'string', ['nullable' => true]);
        $builder->createOneToMany('vaccinations', Vaccination::class)
            ->mappedBy('pet')
            ->cascadeAll()
            ->orphanRemoval()
            ->build()
        ;
    }
}
