<?php
namespace App\Business\Injector;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Site\SiteManager;

class Injector
{
	public function __construct(SiteManager $siteManager)
	{
		$this->siteManager = $siteManager;
	}

	public function inject(BaseJob $job)
	{
		if ($job instanceof SiteManagerAwareInterface) {
			$job->setSiteManager($this->siteManager);
		}

		return $job;
	}
}