<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\SiteCategoryController;
use Tests\Feature\Traits\Auth;
use Tests\TestCase;

class SiteCategoryTest extends TestCase
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
    public function testUpsertSiteCategory() : void
    {
        $faker = \Faker\Factory::create();

        $url = config('app.url') . '/api/category';

        $values = json_decode(file_get_contents(database_path('seeds/json/categories/vinq.json')), true);

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'type' => SiteCategoryController::RESPONSE_TYPES['upsertSiteCategory'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong locale
     *
     * @return void
     */
    public function testUpsertSiteCategoryWrongLocale() : void
    {
        $url = config('app.url') . '/api/category';

        $values = [
            0 => [
                'id' => 184,
                'title' => [
                    'xx_XX' => 'Sale'
                ]
            ]
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/tree'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The title is not a valid locale.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong locale content
     *
     * @return void
     */
    public function testUpsertSiteCategoryWrongLocaleContent() : void
    {
        $url = config('app.url') . '/api/category';

        $values = [
            0 => [
                'id' => 184,
                'title' => [
                    'nl_NL' => null
                ]
            ]
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/tree'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The title.nl_NL field is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong cashback
     *
     * @return void
     */
    public function testUpsertSiteCategoryWrongCashback() : void
    {
        $url = config('app.url') . '/api/category';

        $values = [
            0 => [
                'id' => 184,
                'title' => [
                    'nl_NL' => 'Sale'
                ],
                'cashback' => 'AAA',
            ]
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/tree'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The cashback must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
