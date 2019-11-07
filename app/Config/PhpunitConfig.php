<?php

declare(strict_types=1);

namespace App\Config;

class PhpunitConfig extends DevConfig
{
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['doctrine.dbal.db.options']['connection']['dbname'] = 'petstore_phpunit';

        return $config;
    }

    public function getEnv(): string
    {
        return 'phpunit';
    }
}
