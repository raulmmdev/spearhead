<?php

return [
    'api/site' => [
        'POST' => ['SiteProvider'],
        'PUT' => ['Auth', 'SiteProvider'],
    ],
    'api/category' => [
        'POST' => ['Auth'],
        'PUT' => ['Auth'],
    ],
];
