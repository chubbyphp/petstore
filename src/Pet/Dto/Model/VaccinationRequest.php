<?php

declare(strict_types=1);

namespace App\Pet\Dto\Model;

final readonly class VaccinationRequest
{
    public function __construct(
        public string $name,
    ) {}
}
