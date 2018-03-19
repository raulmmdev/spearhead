<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteTest extends TestCase
{
    protected $siteManager;

    public function setup()
    {
        parent::setUp();

        $this->siteManager = $this->app->make('App\Business\Site\SiteManager');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCreation()
    {
        $faker = \Faker\Factory::create();

        $data = [
            'crud_operation' => ApiRequest::ACTION_CREATE,
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

        $siteJob = new CreateSiteJob();
        $siteJob->setSiteManager($this->siteManager);
        $siteJob->data = $data;
        $siteJob = $this->siteManager->createFromJob($siteJob);

        $this->assertFalse($siteJob->hasErrors());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSiteCreationMissingData()
    {
        $siteJob = new CreateSiteJob();
        $siteJob->setSiteManager($this->siteManager);
        $siteJob = $this->siteManager->createFromJob($siteJob);

        $this->assertTrue($siteJob->hasErrors());
    }
}
