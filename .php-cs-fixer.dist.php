<?php

$finder = PhpCsFixer\Finder::create()
    ->in(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    )
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules(
        [
            // @PhpCsFixer: @PSR12 + @Symfony + some changes used by PhpCsFixer's team
            '@PhpCsFixer' => true,
            '@PHP81Migration' => true,
            '@DoctrineAnnotation' => true,
            // my changes: override some rules in order to make IDEA's PSR12 settings fit to PhpCsFixer's one
            // but IDEA's rearrange code requires changes
            'cast_spaces' => false,
            'blank_line_before_statement' => ['statements' => ['return']], // like @Symfony
            'multiline_whitespace_before_semicolons' => true, // conflicts with IDEA
            'phpdoc_order' => false, // conflicts with IDEA
            'phpdoc_align' => false, // conflicts with IDEA
            'concat_space' => ['spacing' => 'one'],
        ]
    )->setFinder($finder);