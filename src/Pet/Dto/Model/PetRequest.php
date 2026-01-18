<?php

declare(strict_types=1);

namespace App\Pet\Dto\Model;

use App\Core\Dto\Model\ModelRequestInterface;
use App\Core\Model\ModelInterface;
use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;

final class PetRequest implements ModelRequestInterface
{
    public string $name;

    public ?string $tag = null;

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
