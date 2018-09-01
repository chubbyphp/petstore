<?php

declare(strict_types=1);

namespace App\Config;

class CiConfig extends DevConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['doctrine.dbal.db.options']['connection']['dbname'] = 'petshop_ci';

        return $config;
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'ci';
    }
}
