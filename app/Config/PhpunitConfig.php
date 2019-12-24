<?php

declare(strict_types=1);

namespace App\Config;

class PhpunitConfig extends DevConfig
{
    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['doctrine.dbal.db.options']['connection']['dbname'] .= '_phpunit';

        return $config;
    }

    public function getEnv(): string
    {
        return 'phpunit';
    }
}
