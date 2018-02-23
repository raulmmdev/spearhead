<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Business\Message\MessageManager;
use App\Business\Site\SiteManager;

class ApiProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Business\Site\SiteManager', function ($app) {
            return new SiteManager();
        });

        $this->app->bind('App\Business\Message\MessageManager', function ($app) {
            return new MessageManager();
        });
    }
}
