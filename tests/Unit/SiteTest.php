<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
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

		$saveSiteJob = new CreateSiteJob();
		$saveSiteJob->setSiteManager($this->siteManager);

		$saveSiteJob->data['name'] = $faker->company;

		$site = $this->siteManager->createFromJob($saveSiteJob);

		$this->assertNotNull($site);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteCreationMissingData()
	{
		$saveSiteJob = new CreateSiteJob();
		$saveSiteJob->setSiteManager($this->siteManager);

		$site = $this->siteManager->createFromJob($saveSiteJob);

		$this->assertNull($site);
	}
}
