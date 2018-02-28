<?php

namespace Tests\Unit;

use App\Business\Site\SiteManager;
use App\Business\Job\CreateSiteJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InjectorTest extends TestCase
{
	protected $injector;

	public function setup()
	{
		parent::setUp();

		$this->injector = $this->app->make('App\Business\Injector\Injector');
	}

	/**
	 * inject a save site request.
	 *
	 * @return void
	 */
	public function testInjectSaveSite()
	{
		$saveSiteJob = new CreateSiteJob();
		$this->injector->inject($saveSiteJob);

		$this->assertInstanceOf(SiteManager::class, $saveSiteJob->getSiteManager());
	}
}
