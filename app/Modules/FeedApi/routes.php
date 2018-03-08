<?php

Route::group([
    'module' => 'FeedApi',
    'namespace' => 'Modules\FeedApi\Controllers',
    //'middleware' => 'App\Http\Middleware\AuthDigest'
], function () {
    Route::post('/api/site', 'SiteController@createSite');
});
