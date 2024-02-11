<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

final class Pet implements ModelInterface
{
    private string $id;

    private \DateTimeInterface $createdAt;

    private ?\DateTimeInterface $updatedAt = null;

    private ?string $name = null;

    private ?string $tag = null;

    /**
     * @var Collection<int, Vaccination>
     */
    private Collection $vaccinations;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTimeImmutable();
        $this->vaccinations = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
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

    /**
     * @return array{
     *  id: string,
     *  createdAt: \DateTimeInterface,
     *  updatedAt: null|\DateTimeInterface,
     *  name: null|string,
     *  tag: null|string,
     *  vaccinations: array<int<0, max>, array{name: string|null}>
     * }
     */
    public function jsonSerialize(): array
    {
        $vaccinations = [];
        foreach ($this->vaccinations as $vaccination) {
            $vaccinations[] = $vaccination->jsonSerialize();
        }

        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'name' => $this->name,
            'tag' => $this->tag,
            'vaccinations' => $vaccinations,
        ];
    }
}
