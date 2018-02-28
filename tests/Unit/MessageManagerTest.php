<?php

namespace Tests\Unit;

use App\Business\Api\Request\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageManagerTest extends TestCase
{
	protected $messageManager;

	public function setup()
	{
		parent::setUp();

		$this->messageManager = $this->app->make('App\Business\Message\MessageManager');
	}

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPublishCreateSiteMessage()
    {
    	$faker = \Faker\Factory::create();

    	$values = json_encode([
    		'name' => $faker->company,
    	]);

        $result = $this->messageManager->produceJobMessage(ApiRequest::MSG_CREATE_SITE, $values);

        $this->assertTrue($result);
    }
}
