<?php

declare(strict_types=1);

namespace App\Config;

class PhpunitConfig extends DevConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['doctrine.dbal.db.options']['connection']['dbname'] = 'petstore_phpunit';

        return $config;
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'phpunit';
    }
}
