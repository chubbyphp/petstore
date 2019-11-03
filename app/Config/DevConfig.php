<?php

declare(strict_types=1);

namespace App\Config;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Monolog\Logger;

class DevConfig extends ProdConfig
{
    public function getConfig(): array
    {
        $config = parent::getConfig();

        $config['cors']['allow-origin'] = [
            '^https?://localhost:3000' => AllowOriginRegex::class,
        ];

        $config['debug'] = true;

        $config['doctrine.dbal.db.options']['configuration']['cache.result']['type'] = 'array';

        $config['doctrine.orm.em.options']['cache.hydration']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.metadata']['type'] = 'array';
        $config['doctrine.orm.em.options']['cache.query']['type'] = 'array';

        $config['monolog']['level'] = Logger::DEBUG;

        $config['routerCacheFile'] = null;

        return $config;
    }

    protected function getEnv(): string
    {
        return 'dev';
    }
}
