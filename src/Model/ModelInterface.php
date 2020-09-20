<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
{
    public function getId(): string;

    public function getCreatedAt(): \DateTime;

    public function setUpdatedAt(\DateTime $updatedAt): void;

    public function getUpdatedAt(): ?\DateTime;
}
