<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->setPsr4('App\Tests\\', __DIR__);

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

