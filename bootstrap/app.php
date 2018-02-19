<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);


$app->configureMonologUsing(function ($monolog) {
	$options = [
    	'index' => 'laralogs',
    	'type'  => 'log',
	];

	$clientOptions = [
        'host' => null,
        'port' => null,
        'path' => null,
        'url' => null,
        'proxy' => null,
        'transport' => null,
        'persistent' => true,
        'timeout' => null,
        'connections' => [], // host, port, path, timeout, transport, compression, persistent, timeout, config -> (curl, headers, url)
        'roundRobin' => false,
        'log' => false,
        'retryOnConflict' => 0,
        'bigintConversion' => false,
        'username' => null,
        'password' => null,
    ];

	$client = new Elastica\Client();
    $monolog->pushHandler(new Monolog\Handler\ElasticSearchHandler($client, $options));
});


/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
