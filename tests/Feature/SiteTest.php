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

        $values = [
            'name' => $faker->company
        ];

        $url = config('app.url') . '/api/site';

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'SiteProvider' => $this->createAuth($url, $values),
        ];

        $response = $this
            ->withHeaders($headers)
            ->json('POST', $url, $values);

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
        $values = [
            'name' => ''
        ];

        $url = config('app.url') . '/api/site';

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'SiteProvider' => $this->createAuth($url, $values),
        ];

        $response = $this
            ->withHeaders($headers)
            ->json('POST', $url, $values);

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

    /**
     * return a valid header
     *
     * @access private
     * @return string
     */
    private function createAuth(string $url, array $body): string
    {
        $hash_id = 'login';
        $qwindo_key = 'key';
        $timestamp = microtime(true);
        $data = json_encode($body);

        //here we generate the auth token that will allow us to check the authorization for the call
        $token = hash_hmac('sha512', $url.$timestamp.$data, $qwindo_key);
        //generate the authorization base64 encoded
        return base64_encode(sprintf('%s:%s:%s', $hash_id, $timestamp, $token));
    }
}
