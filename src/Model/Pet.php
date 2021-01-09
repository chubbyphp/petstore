<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

final class Pet implements ModelInterface
{
    private string $id;

    private \DateTime $createdAt;

    private ?\DateTime $updatedAt = null;

    private ?string $name = null;

    private ?string $tag = null;

    /**
     * @var Collection<int, Vaccination>
     */
    private Collection $vaccinations;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime();
        $this->vaccinations = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param array<int, Vaccination> $vaccinations
     */
    public function setVaccinations(array $vaccinations): void
    {
        $this->vaccinations->clear();
        foreach ($vaccinations as $vaccination) {
            $vaccination->setPet($this);
            $this->vaccinations->add($vaccination);
        }
    }

    /**
     * @return array<int, Vaccination>
     */
    public function getVaccinations(): array
    {
        return $this->vaccinations->getValues();
    }
}
