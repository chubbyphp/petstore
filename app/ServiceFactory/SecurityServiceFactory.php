<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use App\Repository\UserRepository;
use App\Security\Authentication\PasswordManager;
use App\Security\Authentication\SessionAuthentication;
use Opis\JsonSchema\Validator;
use Psr\Container\ContainerInterface;

final class SecurityServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            PasswordManager::class => static function () {
                return new PasswordManager();
            },
            SessionAuthentication::class => static function (ContainerInterface $container) {
                return new SessionAuthentication(
                    $container->get(UserRepository::class)
                );
            },
            Validator::class => static function () {
                return new Validator();
            },
        ];
    }
}
