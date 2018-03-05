<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\SiteController;
use Tests\TestCase;

class SiteTest extends TestCase
{
    /**
     * Successfull case
     *
     * @return void
     */
    public function testCreateSite() : void
    {
        $faker = \Faker\Factory::create();

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $values = [
            'name' => $faker->company
        ];

        $response = $this
            ->withHeaders($headers)
            ->json('POST', config('app.url') . '/api/site', $values);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'type' => SiteController::RESPONSE_TYPES['createSite'],
                'attributes' => [],
            ]);
    }

    /**
     * Wrong case
     *
     * @return void
     */
    public function testCreateSiteNoName() : void
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $values = [
            'name' => ''
        ];

        $response = $this
            ->withHeaders($headers)
            ->json('POST', config('app.url') . '/api/site', $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/name'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The name field is required.'
                    ]
                ]
            ]);
    }
}
