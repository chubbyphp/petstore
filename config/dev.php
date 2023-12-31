<?php

declare(strict_types=1);

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Monolog\Level;

$config = require __DIR__.'/prod.php';

$config['chubbyphp']['cors']['allowOrigins']['^https?\:\/\/(localhost|127\.\d+.\d+.\d+)(\:\d+)?$'] = AllowOriginRegex::class;
$config['debug'] = true;
$config['doctrine']['cache'] = ['array' => []];
$config['fastroute']['cache'] = null;
$config['monolog']['level'] = Level::Notice;

return $config;
