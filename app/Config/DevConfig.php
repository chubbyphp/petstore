<?php

declare(strict_types=1);

namespace App\Config;

use Monolog\Logger;

class DevConfig extends ProdConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['doctrine.dbal.db.options']['configuration']['cache.result']['type'] = 'array';

        $config['doctrine.orm.em.options']['cache.hydration']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.metadata']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.query']['type'] = 'array';

        $config['monolog']['level'] = Logger::DEBUG;

        return $config;
    }

    /**
     * @return array
     */
    public function getSlimSettings(): array
    {
        $slimSettings = parent::getSlimSettings();

        $slimSettings['displayErrorDetails'] = true;
        $slimSettings['routerCacheFile'] = false;

        return $slimSettings;
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'dev';
    }
}
