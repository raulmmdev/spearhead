<?php

namespace Tests\Unit;

use App\Business\Job\UpsertSiteCategoryJob;
use App\Business\SiteCategory\SiteCategoryManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\ApiFeature;
use App\Model\Entity\SiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteCategoryTest extends TestCase
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Site Category Manager container
     *
     * @access private
     * @var SiteCategoryManager
     */
    protected $siteCategoryManager;

    /**
     * Faker container
     *
     * @access private
     * @var Faker
     */
    private $faker;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Setup current object
     *
     * @access public
     * @return void
     */
    public function setup() : void
    {
        parent::setUp();

        $this->siteCategoryManager = $this->app->make('App\Business\SiteCategory\SiteCategoryManager');

        $this->faker = \Faker\Factory::create();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCategoryUpsert()
    {
        $job = new UpsertSiteCategoryJob();
        $job->setSiteCategoryManager($this->siteCategoryManager);

        $job->data = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,
            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),
            'tree' => json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/categories.json')), true),
        ];

        $job = $this->siteCategoryManager->upsertFromJob($job);

        $this->assertFalse($job->hasErrors());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCategoryUpsertMissingData()
    {
        $job = new UpsertSiteCategoryJob();
        $job->setSiteCategoryManager($this->siteCategoryManager);

        $job = $this->siteCategoryManager->upsertFromJob($job);

        $this->assertTrue($job->hasErrors());
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
