<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\ModelInterface;

interface UserInterface extends ModelInterface
{
    public function getUsername(): string;

    public function getPassword(): string;

    public function getHash(): string;
}
