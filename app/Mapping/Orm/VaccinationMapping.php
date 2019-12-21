<?php

declare(strict_types=1);

namespace App\Mapping\Orm;

use App\Model\Pet;
use Chubbyphp\DoctrineDbServiceProvider\Driver\ClassMapMappingInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

final class VaccinationMapping implements ClassMapMappingInterface
{
    public function configureMapping(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('vaccination');
        $builder->createField('id', 'guid')->isPrimaryKey()->build();
        $builder->addField('name', 'string');
        $builder->addManyToOne('pet', Pet::class, 'vaccinations');
    }
}
