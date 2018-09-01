<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt();
}
