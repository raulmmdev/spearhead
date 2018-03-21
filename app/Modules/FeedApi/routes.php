<?php

Route::group([
    'module' => 'FeedApi',
    'namespace' => 'Modules\FeedApi\Controllers',
    //'middleware' => 'App\Http\Middleware\AuthDigest'
    'middleware' => 'App\Http\Middleware\AuthBasic'
], function () {
    Route::prefix('api')->group(function () {
        Route::post('site', 'SiteController@create')->name('createSite');
        Route::delete('site/{siteId}', 'SiteController@delete')->name('deleteSite');

        Route::match(['post', 'put'], 'category', 'SiteCategoryController@upsert')->name('upsertCategory');

        Route::match(['post', 'put'], 'product', 'ProductController@upsert')->name('upsertProduct');
        Route::delete('product', 'ProductController@delete')->name('deleteProduct');
    });
});
