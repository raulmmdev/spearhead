<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\SiteProductController;
use Tests\Feature\Traits\Auth;
use Tests\TestCase;

class SiteProductTest extends TestCase
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
    public function testDeleteSiteProduct() : void
    {
        $faker = \Faker\Factory::create();

        $url = config('app.url') . '/api/product';

        $products = json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/products-57.json')), true);

        $values = [
            'id' => $products[0]['product_id'],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('DELETE', $url, $values);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'type' => SiteProductController::RESPONSE_TYPES['delete'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong locale
     *
     * @return void
     */
    public function testDeleteSiteProductWrongId() : void
    {
        $url = config('app.url') . '/api/product';

        $values = [
            'id' => '999',
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('DELETE', $url, $values);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product.id'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The selected product.id is invalid.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
