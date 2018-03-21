<?php

return [
    'createSite' => ['SiteProvider'],
    'deleteSite' => ['SiteProvider'],
    'updateSite' => ['Auth', 'SiteProvider'],
    'upsertCategory' => ['Auth'],
    'upsertProduct' => ['Auth'],
    'deleteProduct' => ['Auth'],
];
