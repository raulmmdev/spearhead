<?php

namespace Tests\Unit;

use App\Business\Job\UpsertSiteCategoryJob;
use App\Business\SiteCategory\SiteCategoryManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\Site;
use App\Model\Entity\SiteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiteCategoryTest extends TestCase
{
	protected $siteManager;

	public function setup()
	{
		parent::setUp();

		$this->siteCategoryManager = $this->app->make('App\Business\SiteCategory\SiteCategoryManager');
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteCategoryUpsert()
	{
		$job = new UpsertSiteCategoryJob();
		$job->setSiteCategoryManager($this->siteCategoryManager);
		$job->data = [
            'crud_operation' => ApiRequest::ACTION_UPSERT,

            'site' => [
            	'id' => Site::pluck('id')[0]
            ],

            'tree' => json_decode(file_get_contents(database_path('seeds/json/categories/vinq.json')), true),
        ];

        $job = $this->siteCategoryManager->upsertFromJob($job);

		$this->assertFalse($job->hasErrors());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSiteCategoryUpsertMissingData()
	{
		$job = new UpsertSiteCategoryJob();
		$job->setSiteCategoryManager($this->siteCategoryManager);

        $job = $this->siteCategoryManager->upsertFromJob($job);

		$this->assertTrue($job->hasErrors());
	}
}
