<?php

Route::group([
    'module' => 'FeedApi',
    'namespace' => 'Modules\FeedApi\Controllers',
    //'middleware' => 'App\Http\Middleware\AuthDigest'
    'middleware' => 'App\Http\Middleware\AuthBasic'
], function () {
    Route::post('/api/site', 'SiteController@createSite');
});
