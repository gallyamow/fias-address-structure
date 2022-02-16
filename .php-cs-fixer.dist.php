<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['var', 'tmp'])
    ->notPath('config/bundles.php')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        // >>> required by symfony cs
        // @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/3988
        'single_line_throw' => false,
        // <<< required by symfony cs,
        // >>> my project
        'class_definition' => [
            'single_item_single_line' => true,
        ],
        'cast_spaces' => false,
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        // <<< my project
    ]
)->setFinder($finder);
