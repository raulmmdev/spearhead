<?php

return [
    'api/site' => [
        'POST' => ['SiteProvider'],
        'PUT' => ['Auth', 'SiteProvider'],
    ],
    'api/categories/data' => [
        'POST' => ['Auth'],
        'PUT' => ['Auth'],
    ],
];
