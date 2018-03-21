<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Job\UpsertProductJob;
use App\Business\Job\UpsertSiteCategoryJob;
use App\Business\Product\ProductManager;
use App\Business\Site\SiteManager;
use App\Business\SiteCategory\SiteCategoryManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\ApiFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobFactoryTest extends TestCase
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    protected $jobFactory;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Setup current object
     *
     * @access public
     * @return void
     */
    public function setup()
    {
        parent::setUp();

        $this->jobFactory = $this->app->make('App\Business\Job\JobFactory');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid save site job.
     *
     * @return void
     */
    public function testCreateSaveSiteJob()
    {
        $faker = \Faker\Factory::create();

        $values = [
            'crud_operation' => ApiRequest::ACTION_CREATE,
            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),
            'site' => [
                'name' => $faker->company,
            ],
        ];

        $job = $this->jobFactory->create(ApiRequest::QUEUE_SITE, $values);

        $this->assertInstanceOf(CreateSiteJob::class, $job);
        $this->assertInstanceOf(SiteManager::class, $job->getSiteManager());
        $this->assertEquals($values['site']['name'], $job->data['site']['name']);
        $this->assertEmpty($job->getErrors());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid upsert category job.
     *
     * @return void
     */
    public function testUpsertSiteCategoryJob()
    {
        $faker = \Faker\Factory::create();

        $values = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,
            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),
            'tree' => json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/categories.json')), true),
        ];

        $job = $this->jobFactory->create(ApiRequest::QUEUE_CATEGORY, $values);

        $this->assertInstanceOf(UpsertSiteCategoryJob::class, $job);
        $this->assertInstanceOf(SiteCategoryManager::class, $job->getSiteCategoryManager());
        $this->assertEquals($values['tree'], $job->data['tree']);
        $this->assertEmpty($job->getErrors());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Creates a valid upsert product job.
     *
     * @return void
     */
    public function testUpsertProductJob()
    {
        $faker = \Faker\Factory::create();

        $product = json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/products-57.json')), true)[0];
        $product['product_id'] = $faker->randomNumber();

        $values = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,
            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),
            'product' => $product,
        ];

        $job = $this->jobFactory->create(ApiRequest::QUEUE_PRODUCT, $values);

        $this->assertInstanceOf(UpsertProductJob::class, $job);
        $this->assertInstanceOf(ProductManager::class, $job->getProductManager());
        $this->assertEquals($values['product']['product_id'], $job->data['product']['product_id']);
        $this->assertEmpty($job->getErrors());
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
