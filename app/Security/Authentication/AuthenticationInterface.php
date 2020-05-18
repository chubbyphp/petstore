<?php

declare(strict_types=1);

namespace App\Security\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationInterface
{
    public function getType(): string;

    public function isResponsible(ServerRequestInterface $request): bool;

    public function isAuthenticated(ServerRequestInterface $request): bool;
}
