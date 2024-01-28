<?php

declare(strict_types=1);

namespace App\Dto\Model;

use App\Model\ModelInterface;
use App\Model\Pet;
use App\Model\Vaccination;

final class PetRequest implements ModelRequestInterface
{
    public string $name;

    public null|string $tag;

    /**
     * @var array<VaccinationRequest>
     */
    public array $vaccinations;

    public function createModel(): ModelInterface
    {
        $vaccinations = [];
        foreach ($this->vaccinations as $vaccinationRequest) {
            $vaccination = new Vaccination();
            $vaccination->setName($vaccinationRequest->name);

            $vaccinations[] = $vaccination;
        }

        $model = new Pet();
        $model->setName($this->name);
        $model->setTag($this->tag);
        $model->setVaccinations($vaccinations);

        return $model;
    }

    /**
     * @param Pet $model
     */
    public function updateModel(ModelInterface $model): ModelInterface
    {
        $vaccinations = [];
        foreach ($this->vaccinations as $vaccinationRequest) {
            $vaccination = new Vaccination();
            $vaccination->setName($vaccinationRequest->name);

            $vaccinations[] = $vaccination;
        }

        $model->setUpdatedAt(new \DateTimeImmutable());
        $model->setName($this->name);
        $model->setTag($this->tag);
        $model->setVaccinations($vaccinations);

        return $model;
    }
}
