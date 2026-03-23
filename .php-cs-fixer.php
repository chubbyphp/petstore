<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

/** @var array $config */
$config = require_once __DIR__ . '/vendor/chubbyphp/chubbyphp-dev-helper/phpcs.php';

return (new PhpCsFixer\Config)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setIndent($config['indent'])
    ->setLineEnding($config['lineEnding'])
    ->setRules($config['rules'])
    ->setRiskyAllowed($config['riskyAllowed'])
    ->setFinder($finder)
;
