<?php

namespace Tests\Feature;

use App\Business\Api\Request\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
                'type' => ApiRequest::MSG_DESCRIPTIONS[ApiRequest::MSG_CREATE_SITE],
                'attributes' => [],
            ]);
    }
}
