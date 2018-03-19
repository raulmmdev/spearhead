<?php

namespace Tests\Unit;

use App\Business\Job\CreateSiteJob;
use App\Business\Site\SiteManager;
use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobFactoryTest extends TestCase
{
    protected $jobFactory;

    public function setup()
    {
        parent::setUp();

        $this->jobFactory = $this->app->make('App\Business\Job\JobFactory');
    }

    /**
     * create a valid save site job.
     *
     * @return void
     */
    public function testCreateSaveSiteJob()
    {
        $faker = \Faker\Factory::create();

        $values = [
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

        $saveSiteJob = $this->jobFactory->create(ApiRequest::QUEUE_SITE, $values);

        $this->assertInstanceOf(CreateSiteJob::class, $saveSiteJob);
        $this->assertInstanceOf(SiteManager::class, $saveSiteJob->getSiteManager());
        $this->assertEquals($values['merchant'], $saveSiteJob->data['merchant']);
        $this->assertEquals($values['site'], $saveSiteJob->data['site']);
        $this->assertEmpty($saveSiteJob->getErrors());
    }
}
