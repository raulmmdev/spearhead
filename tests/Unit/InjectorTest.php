<?php

namespace Tests\Unit;

use App\Business\Site\SiteManager;
use App\Http\Requests\Qwindo\SaveSiteRequest;
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
    	$request = new SaveSiteRequest();
    	$this->injector->inject($request);

        $this->assertInstanceOf(SiteManager::class, $request->getSiteManager());
    }
}
