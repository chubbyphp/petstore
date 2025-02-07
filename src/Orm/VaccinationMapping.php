<?php

declare(strict_types=1);

namespace App\Orm;

use App\Model\Pet;
use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class VaccinationMapping implements ClassMapMappingInterface
{
    /**
     * @param ORMClassMetadata $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('vaccination');
        $builder->createField('id', Types::GUID)->makePrimaryKey()->build();
        $builder->addField('name', Types::STRING);
        $builder->createManyToOne('pet', Pet::class)
            ->inversedBy('vaccinations')
            ->addJoinColumn('pet_id', 'id', false, false, 'CASCADE')
            ->build()
        ;
    }
}
