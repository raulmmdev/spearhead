<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteTest extends TestCase
{
	protected $siteManager;

	public function setup()
	{
		parent::setUp();

		$this->siteManager = $this->app->make('App\Business\Site\SiteManager');
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteCreation()
	{
		$faker = \Faker\Factory::create();

		$siteJob = new CreateSiteJob();
		$siteJob->setSiteManager($this->siteManager);
		$siteJob->data['crud_operation'] = ApiRequest::ACTION_CREATE;
		$siteJob->data['name'] = $faker->company;
		$siteJob = $this->siteManager->createFromJob($siteJob);

		$this->assertFalse($siteJob->hasErrors());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteCreationMissingData()
	{
		$siteJob = new CreateSiteJob();
		$siteJob->setSiteManager($this->siteManager);
		$siteJob = $this->siteManager->createFromJob($siteJob);

		$this->assertTrue($siteJob->hasErrors());
	}
}
