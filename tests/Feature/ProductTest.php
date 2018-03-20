<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use App\Model\Entity\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\ProductController;
use Tests\Feature\Traits\Auth;
use Tests\TestCase;

class ProductTest extends TestCase
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
    public function testCreateProduct() : void
    {
        $url = config('app.url') . '/api/product';

        $products = json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/products-57.json')), true);

        $values = $products[0];
        $values['product_id'] = $this->faker->randomNumber();

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($url, $values))
            ->json('POST', $url, $values);

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'type' => ProductController::RESPONSE_TYPES['create'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Successfull case
     *
     * @return void
     */
    public function testDeleteProduct() : void
    {
        if (Product::count()) {
            $url = config('app.url') . '/api/product';

            $values = [
                'product_id' => Product::pluck('source_id')[0],
            ];

            $response = $this
                ->withHeaders($this->getHeadersAsAuth($url, $values))
                ->json('DELETE', $url, $values);

            $response
                ->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'type' => ProductController::RESPONSE_TYPES['delete'],
                    'attributes' => [],
                ]);
        } else {
            // No any product on DB => Nothing to delete
            $this->assertTrue(true);
        }
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Wrong Product Id
     *
     * @return void
     */
    public function testDeleteProductWrongId() : void
    {
        $url = config('app.url') . '/api/product';

        $values = [
            'product_id' => 'AAA',
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
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The product id must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
