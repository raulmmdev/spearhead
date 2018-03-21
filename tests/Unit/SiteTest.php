<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\ApiFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteTest extends TestCase
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Site Manager container
     *
     * @access protected
     * @var SiteManager
     */
    protected $siteManager;

    /**
     * Faker container
     *
     * @access protected
     * @var Faker
     */
    protected $faker;

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

        $this->siteManager = $this->app->make('App\Business\Site\SiteManager');

        $this->faker = \Faker\Factory::create();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCreation()
    {
        $job = new CreateSiteJob();
        $job->setSiteManager($this->siteManager);
        $job->data = [
            'crud_operation' => ApiRequest::ACTION_CREATE,

            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),

            'site' => [
                'name' => $this->faker->company,
            ],
        ];
        $job = $this->siteManager->createFromJob($job);

        $this->assertFalse($job->hasErrors());
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCreationMissingData()
    {
        $job = new CreateSiteJob();
        $job->setSiteManager($this->siteManager);
        $job = $this->siteManager->createFromJob($job);

        $this->assertTrue($job->hasErrors());
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
