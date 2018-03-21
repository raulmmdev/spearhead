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
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

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
    public function setup() : void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Successfull case
     *
     * @return void
     */
    public function testUpsertSiteCategory() : void
    {
        $url = config('app.url') . '/api/category';

        $values = json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/categories.json')), true);

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'type' => SiteCategoryController::RESPONSE_TYPES['upsert'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong locale content
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
                    'nlx_NL' => null
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
                        'details' => 'The title[nlx_NL] is not a valid locale.'
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
                        'details' => 'The title[nl_NL] is required.'
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
                        'details' => 'The tree[cashback] must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong children structure
     *
     * @return void
     */
    public function testUpsertSiteCategoryWrongChildren() : void
    {
        $url = config('app.url') . '/api/category';

        $values = [
            0 => [
                'id' => 184,
                'title' => [
                    'nl_NL' => 'Sale'
                ],
                'cashback' => 5,
                'children' => [
                    0 => [
                        'id' => 'AAA',
                    ]
                ],
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
                        'details' => 'The children[title] is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
