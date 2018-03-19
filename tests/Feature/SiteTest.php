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
            'site' => [
                'portal_payment_methods' => [
                    'AMEX' => 'AMEX',
                    'WALLET' => 'WALLET',
                    'MAESTRO' => 'MAESTRO',
                    'VVVGIFTCRD' => 'VVVGIFTCRD',
                    'IDEAL' => 'IDEAL',
                    'FASHIONCHQ' => 'FASHIONCHQ',
                    'MASTERCARD' => 'MASTERCARD',
                    'MISTERCASH' => 'MISTERCASH',
                    'VISA' => 'VISA'
                ],
                'portal_keurmerk_qshops' => 1,
                'portal_url' => 'https://www.falkewinkel.nl',
                'site_apikey' => 'a052e6e31d7514ba727a3b2478a74e20893bb042',
                'portal_description' => 'VINQ.nl / FALKEwinkel',
                'feed_type' => '',
                'portal_keurmerk_thuiswinkel' => 0,
                'site_id' => 41343,
                'supportemail' => '',
                'feed_url' => '',
                'ca_code' => '108',
                'supportphone' => '',
                'qwindo_integration' => true,
                'portal_fastcheckout' => 0,
                'mcc' => '5655',
                'portal_category' => '108',
                'site_status' => 'blocked'
            ],

            'merchant' => [
                'country' => 'NL',
                'email_address' => 'pieter@vinq.nl',
                'name' => $faker->company,
                'merchant_id' => 10352732,
                'merchant_status' => 'active'
            ],
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
    /*public function testCreateSiteNoMerchant() : void
    {
        $url = config('app.url') . '/api/site';

        $values = [
            'merchant' => ''
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
                            'pointer' => '/data/attributes/merchant'
                        ],
                        'title' => 'Invalid Attribute',
                        'details' => 'The name field is required.'
                    ]
                ]
            ]);
    }*/

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
