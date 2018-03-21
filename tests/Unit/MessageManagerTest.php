<?php

namespace Tests\Unit;

use App\Http\Requests\ApiRequest;
use App\Model\Entity\ApiFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageManagerTest extends TestCase
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    protected $messageManager;

    /**
     * Faker container
     *
     * @access private
     * @var Faker
     */
    private $faker;

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

        $this->messageManager = $this->app->make('App\Business\Message\MessageManager');

        $this->faker = \Faker\Factory::create();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPublishCreateSiteMessage()
    {
        $faker = \Faker\Factory::create();

        $values = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,

            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),

            'site' => [
                'name' => $faker->company,
            ]
        ];

        $result = $this->messageManager->produceJobMessage(ApiRequest::QUEUE_SITE, ApiRequest::ACTION_CREATE, $values);

        $this->assertNotNull($result);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
