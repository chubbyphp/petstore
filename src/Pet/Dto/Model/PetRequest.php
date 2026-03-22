<?php

declare(strict_types=1);

namespace App\Pet\Dto\Model;

use App\Pet\Model\Pet;
use App\Pet\Model\Vaccination;
use Chubbyphp\Api\Dto\Model\ModelRequestInterface;
use Chubbyphp\Api\Model\ModelInterface;

final readonly class PetRequest implements ModelRequestInterface
{
    /**
     * @param array<VaccinationRequest> $vaccinations
     */
    public function __construct(
        public string $name,
        public ?string $tag,
        public array $vaccinations,
    ) {}

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
