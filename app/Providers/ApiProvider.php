<?php

namespace App\Providers;

use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Injector\Injector;
use App\Business\Job\JobFactory;
use App\Business\Message\MessageManager;
use App\Business\Product\ProductManager;
use App\Business\Site\SiteManager;
use App\Business\SiteCategory\SiteCategoryManager;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind('App\Business\Product\ProductManager', function ($app) {
            return new ProductManager(
                $app->make('App\Model\Entity\Repository\ApiFeatureRepository'),
                $app->make('App\Model\Entity\Repository\ProductRepository'),
                $app->make('App\Model\Entity\Repository\ProductVariantRepository'),
                $app->make('App\Model\Entity\Repository\SiteCategoryRepository'),
                $app->make('App\Business\Product\Attribute\ProductAttributeManager'),
                $app->make('App\Business\ProductVariant\ProductVariantManager')
            );
        });

        $this->app->bind('App\Business\SiteCategory\SiteCategoryManager', function ($app) {
            return new SiteCategoryManager(
                $app->make('App\Model\Entity\Repository\ApiFeatureRepository'),
                $app->make('App\Model\Entity\Repository\SiteCategoryRepository')
            );
        });

        $this->app->bind('App\Business\Api\Response\ApiResponseManager', function ($app) {
            return new ApiResponseManager();
        });

        $this->app->bind('App\Business\Injector\Injector', function ($app) {
            return new Injector(
                $app->make('App\Business\Site\SiteManager'),
                $app->make('App\Business\SiteCategory\SiteCategoryManager'),
                $app->make('App\Business\Product\ProductManager')
            );
        });

        $this->app->bind('App\Business\Message\MessageManager', function ($app) {
            return new MessageManager(
                $app->make('App\Business\Job\JobFactory'),
                $app->make('App\Business\BusinessLog\BusinessLogManager')
            );
        });

        $this->app->bind('App\Business\Job\JobFactory', function ($app) {
            return new JobFactory(
                $app->make('App\Business\Injector\Injector'),
                $app->make('App\Business\BusinessLog\BusinessLogManager')
            );
        });

        $this->app->bind('App\Business\BusinessLog\BusinessLogManager', function ($app) {
            return new BusinessLogManager();
        });
    }
}
