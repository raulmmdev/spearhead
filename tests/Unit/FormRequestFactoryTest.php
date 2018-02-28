<?php

namespace Tests\Unit;

use App\Business\Api\Request\ApiRequest;
use App\Business\Site\SiteManager;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormRequestFactoryTest extends TestCase
{
	protected $formRequestFactory;

	public function setup()
	{
		parent::setUp();

		$this->formRequestFactory = $this->app->make('App\Business\FormRequest\FormRequestFactory');
	}

    /**
     * create a valid save site request.
     *
     * @return void
     */
    public function testCreateSaveSiteRequest()
    {
    	$faker = \Faker\Factory::create();

    	$values = [
    		'name' => $faker->company,
    	];

    	$saveSiteRequest = $this->formRequestFactory->create(ApiRequest::MSG_CREATE_SITE, $values);

    	$this->assertInstanceOf(SaveSiteRequest::class, $saveSiteRequest);
    	$this->assertInstanceOf(SiteManager::class, $saveSiteRequest->getSiteManager());
    	$this->assertEquals($values['name'], $saveSiteRequest['name']);
    	$this->assertEmpty($saveSiteRequest->getErrors());
    }
}
