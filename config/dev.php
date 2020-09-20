<?php

declare(strict_types=1);

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Laminas\Config\Doctrine\ServiceFactory\Common\Cache\ArrayCacheFactory;
use Monolog\Logger;

$config = require __DIR__.'/prod.php';

$config['chubbyphp']['cors']['allowOrigins']['^https?://localhost'] = AllowOriginRegex::class;
$config['debug'] = true;
$config['dependencies']['factories'][Cache::class] = ArrayCacheFactory::class;
$config['fastroute']['cache'] = null;
$config['monolog']['level'] = Logger::DEBUG;

return $config;
