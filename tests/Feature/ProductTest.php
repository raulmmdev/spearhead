<?php

namespace Tests\Feature;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Job\UpsertProductJob;
use App\Business\Product\ProductManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\ApiFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\FeedApi\Controllers\ProductController;
use Tests\Feature\Traits\Auth;
use Tests\TestCase;

class SiteProductTest extends TestCase
{
    use Auth;

    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Product Manager container
     *
     * @access protected
     * @var ProductManager
     */
    protected $productManager;

    /**
     * Faker container
     *
     * @access protected
     * @var Faker
     */
    protected $faker;

    /**
     * URL to perform the task
     *
     * @access protected
     * @var string
     */
    protected $url;

    /**
     * Product container
     *
     * @access protected
     * @var array
     */
    protected $product;

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

        $this->productManager = $this->app->make('App\Business\Product\ProductManager');

        $this->faker = \Faker\Factory::create();

        $this->url = config('app.url') . '/api/product';

        $this->product = json_decode(file_get_contents(database_path('seeds/json/vinq-6205-541/products-57.json')), true)[0];
        $this->product['product_id'] = $this->faker->randomNumber();
    }

    //------------------------------------------------------------------------------------------------------------------
    // CASE: SUCCESS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Successfull test before
     *
     * @return void
     */
    public function testSuccessBeforeRabbitMQ() : void
    {
        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'type' => ProductController::RESPONSE_TYPES['upsert'],
                'attributes' => [],
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Successfull test after
     *
     * @return void
     */
    public function testSuccessAfterRabbitMQ() : void
    {
        $job = new UpsertProductJob();
        $job->setProductManager($this->productManager);
        $job->data = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,
            'user' => ApiFeature::find(ApiFeature::pluck('id')[0]),
            'product' => $this->product,
        ];
        $job = $this->productManager->upsertFromJob($job);

        $this->assertInstanceOf(UpsertProductJob::class, $job);
        $this->assertInstanceOf(ProductManager::class, $job->getProductManager());
        $this->assertEquals($this->product['product_id'], $job->data['product']['product_id']);
        $this->assertEmpty($job->getErrors());
    }

    //------------------------------------------------------------------------------------------------------------------
    // CASE: FAIL
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductId() : void
    {
        $this->product['product_id'] = '[ERROR]';

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

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

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductName() : void
    {
        $this->product['product_name'] = null;

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The product name field is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongDownloadable() : void
    {
        $this->product['downloadable'] = '[ERROR]';

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The downloadable field must be true or false.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductSkuNumber() : void
    {
        $this->product['sku_number'] = null;

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The sku number field is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductWeight() : void
    {
        $this->product['weight'] = '[ERROR]';

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The weight must be a number.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductWeightUnit() : void
    {
        $this->product['weight_unit'] = 'lb';

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The selected weight unit is invalid.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductCategoryIds() : void
    {
        $this->product['category_ids'] = [1,2,3,'[ERROR]'];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The category_ids[3] must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongProductImageUrls() : void
    {
        $this->product['product_image_urls'] = [
            0 => ['url' => '[ERROR]', 'main' => true],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The product_image_urls[0][url] format is invalid.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongShortDescription() : void
    {
        $locale = $this->faker->locale();

        $this->product['short_product_description'] = [
            $locale => null,
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The short_product_description['. $locale .'] is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongLongDescription() : void
    {
        $this->product['long_product_description'] = [
            'xx_XX' => $this->faker->text(),
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The long_product_description[xx_XX] is not a valid locale code.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongSalePrice() : void
    {
        $this->product['sale_price'] = null;

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The sale price field is required.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongRetailPrice() : void
    {
        $this->product['retail_price'] = '[ERROR]';

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The retail price must be a number.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongTax() : void
    {
        $country = $this->faker->countryCode();

        $this->product['tax'] = [
            'name' => $this->faker->text(),
            'id' => $this->faker->randomDigit(),
            'rules' => [
                $country => $this->faker->text(),
                'XX' => $this->faker->randomFloat(),
            ],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The tax[rules]['. $country .'] must be a number.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongStock() : void
    {
        $this->product['stock'] = null;

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The stock must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongMetadata() : void
    {
        $locale = $this->faker->locale();

        $this->product['metadata'] = [
            'title' => [
                $locale => $this->faker->text(),
                'xx_XX' => $this->faker->text(),
            ],
            'keyword' => [
                $locale => $this->faker->text(),
                'xx_XX' => $this->faker->text(),
            ],
            '[ERROR]' => [
                $locale => $this->faker->text(),
                'xx_XX' => $this->faker->text(),
            ],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The metadata[title][xx_XX] is not a valid locale code.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongAttributes() : void
    {
        $attribute = str_slug($this->faker->text(), '-');

        $this->product['attributes'] = [
            $attribute => [
                'xx_XX' => [
                    'label' => $this->faker->text(),
                    'value' => $this->faker->text(),
                ]
            ],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The attributes['. $attribute .'][xx_XX] is not a valid locale code.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Fail test before
     *
     * @return void
     */
    public function testFailBeforeRabbitMQWrongVariants() : void
    {
        $this->product['variants'] = [
            0 => [
                'product_id' => '[ERROR]',
            ],
        ];

        $response = $this
            ->withHeaders($this->getHeadersAsAuth($this->url, $this->product))
            ->json('POST', $this->url, $this->product);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    [
                        'source' => [
                            'pointer' => '/data/attributes/product'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The variants[product id] must be an integer.'
                    ]
                ]
            ]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
