<?php

Route::group([
	'module' => 'FeedApi',
	'namespace' => 'Modules\FeedApi\Controllers',
], function() {
	Route::post('/api/site', 'SiteController@createSite');
});