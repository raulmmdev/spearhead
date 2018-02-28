<?php

namespace Tests\Unit;

use App\Http\Requests\Qwindo\SaveSiteRequest;
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

		$values = [
			'name' => $faker->company,
		];

		$result = $this->messageManager->produceJobMessage(SaveSiteRequest::QUEUE, $values);

		$this->assertTrue($result);
	}
}
