<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\SiteController;
use Tests\Feature\Traits\Auth;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use Auth;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Successfull case
     *
     * @return void
     */
    public function testCreateSite() : void
    {
        $faker = \Faker\Factory::create();

        $url = config('app.url') . '/api/site';

        $values = [
            'name' => $faker->company
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsSiteProvider($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'type' => SiteController::RESPONSE_TYPES['createSite'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong case
     *
     * @return void
     */
    public function testCreateSiteNoName() : void
    {
        $url = config('app.url') . '/api/site';

        $values = [
            'name' => ''
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsSiteProvider($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/site'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The name field is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
