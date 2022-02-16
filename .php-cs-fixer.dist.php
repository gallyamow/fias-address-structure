<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['var', 'tmp'])
    ->notPath('config/bundles.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@PSR12' => true,
    ]
)->setFinder($finder);
