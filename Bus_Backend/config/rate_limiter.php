<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default API Rate Limits
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default rate limits for API requests.
    | Different rate limits can be defined for different user roles.
    |
    */

    'api' => [
        'default' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'authenticated' => [
            'max_attempts' => 120,
            'decay_minutes' => 1,
        ],
        'admin' => [
            'max_attempts' => 300,
            'decay_minutes' => 1,
        ],
        'role_based' => [
            'admin' => 300,
            'teacher' => 120,
            'parent' => 100,
            'student' => 60,
            'driver' => 100,
            'cleaner' => 100,
        ],
    ],

];