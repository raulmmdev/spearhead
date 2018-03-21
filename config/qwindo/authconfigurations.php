<?php

return [
    'api/site' => [
        'PUT'    => ['Auth', 'SiteProvider'],
        'POST'   => ['SiteProvider'],
    ],
    'api/category' => [
        'PUT'    => ['Auth'],
        'POST'   => ['Auth'],
    ],
    'api/product' => [
        'PUT'    => ['Auth'],
        'POST'   => ['Auth'],
        'DELETE' => ['Auth'],
    ],
];
