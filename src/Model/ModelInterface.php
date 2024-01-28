<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface extends \JsonSerializable
{
    public function getId(): string;

    public function getCreatedAt(): \DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void;

    /**
     * @return null|\DateTime|\DateTimeImmutable
     */
    public function getUpdatedAt(): ?\DateTimeInterface;
}
