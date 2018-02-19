<?php

namespace Tests\Unit;

use App\Business\Site\SiteManager;
use App\Http\Requests\SaveSite;
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
    	$request = new SaveSite($this->siteManager);
    	$request['name'] = 'The site name of the test';
    	$site = $this->siteManager->createSiteFromRequest($request);

        $this->assertNotNull($site);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCreationMissingData()
    {
    	$request = new SaveSite($this->siteManager);
    	$site = $this->siteManager->createSiteFromRequest($request);

        $this->assertNull($site);
    }
}
