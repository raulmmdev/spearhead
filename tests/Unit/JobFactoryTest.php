<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobFactoryTest extends TestCase
{
	protected $jobFactory;

	public function setup()
	{
		parent::setUp();

		$this->jobFactory = $this->app->make('App\Business\Job\JobFactory');
	}

    /**
     * create a valid save site job.
     *
     * @return void
     */
    public function testCreateSaveSiteJob()
    {
    	$faker = \Faker\Factory::create();

    	$values = [
    		'name' => $faker->company,
    	];

    	$saveSiteJob = $this->jobFactory->create(SaveSiteRequest::QUEUE, $values);

    	$this->assertInstanceOf(CreateSiteJob::class, $saveSiteJob);
    	$this->assertInstanceOf(SiteManager::class, $saveSiteJob->getSiteManager());
    	$this->assertEquals($values['name'], $saveSiteJob->data['name']);
    	$this->assertEmpty($saveSiteJob->getErrors());
    }
}
