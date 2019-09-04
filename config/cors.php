<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials'    => true,
    'allowedOrigins'         => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders'         => [
        'Origin',
        'Content-Type',
        'Authorization',
        'X-Authorization',
    ],
    'allowedMethods'         => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],
    'exposedHeaders'         => [],
    'maxAge'                 => 0,

];