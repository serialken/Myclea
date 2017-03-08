<?php

return \Symfony\CS\Config::create()
    ->fixers([
        'ordered_use',
        'short_array_syntax',
        'align_double_arrow',
        'align_equals',
        'phpdoc_order',
    ])
    ->finder(
        \Symfony\CS\Finder::create()
            ->in(__DIR__.'/src')
            ->exclude('phpcas')
    );
