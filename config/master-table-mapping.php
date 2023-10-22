<?php

use Schepeis\Mapping\CompareProviders\Gpt35;
use Schepeis\Mapping\CompareProviders\JaroWinkler;
use Schepeis\Mapping\CompareProviders\Levenshtein;
use Schepeis\Mapping\CompareProviders\SimilarText;
use Schepeis\Mapping\CompareProviders\SmithWatermanGotoh;

return [

    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    |
    | Default provider
    |
    */
    'default' => 'similarText',

    'providers' => [
        'similarText' => [
            'handler' => SimilarText::class,
        ],
        'levenshtein' => [
            'handler' => Levenshtein::class,
        ],
        'jaroWinkler' => [
            'handler' => JaroWinkler::class,
        ],
        'smg' => [
            'handler' => SmithWatermanGotoh::class,
        ],
        'gpt35' => [
            'handler' => Gpt35::class,
        ],
    ],
];
