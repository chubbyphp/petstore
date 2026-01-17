<?php

declare(strict_types=1);

namespace App\Pet\Orm;

use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;
use Chubbyphp\Laminas\Config\Doctrine\Persistence\Mapping\Driver\ClassMapMappingInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata;

final class VaccinationMapping implements ClassMapMappingInterface
{
    /**
     * @param ClassMetadata<Vaccination> $metadata
     */
    public function configureMapping(ClassMetadata $metadata): void
    {
        /** @var ORMClassMetadata<Vaccination> $metadata */
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
