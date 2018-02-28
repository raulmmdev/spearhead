<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\SiteController;
use Tests\TestCase;

class SiteTest extends TestCase
{
    /**
     * create a 'create site' request to qwindo.
     *
     * @return void
     */
    public function testCreateSite()
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
            ->json('POST', '/api/site', $values);

        $response
            ->assertStatus(201)
            ->assertJson([
                'type' => SiteController::RESPONSE_TYPES['createSite'],
                'attributes' => [],
            ]);
    }
}
