<?php

Route::group([
    'module' => 'FeedApi',
    'namespace' => 'Modules\FeedApi\Controllers',
    //'middleware' => 'App\Http\Middleware\AuthDigest'
    'middleware' => 'App\Http\Middleware\AuthBasic'
], function () {
    Route::prefix('api')->group(function () {
        Route::post('site', 'SiteController@createSite');
        Route::match(['post', 'put'], 'categories/data', 'SiteCategoryController@upsertSiteCategory');
    });
});
