<?php

return [

    'default' => 'similarText',

    'providers' => [
        'similarText' => [
            'callable' => [\Atomescrochus\StringSimilarities\Compare::class, 'similarText'],
        ],
        'jaroWinkler' => [
            'callable' => [\Atomescrochus\StringSimilarities\Compare::class, 'jaroWinkler'],
            ],
        'levenshtein' => [
            'callable' => [\Atomescrochus\StringSimilarities\Compare::class, 'levenshtein'],
            ],
        'smithWatermanGotoh' => [
            'callable' => [\Atomescrochus\StringSimilarities\Compare::class, 'smg'],
            ],
        'gpt35' => [],
    ],
];
