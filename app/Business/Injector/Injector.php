<?php

namespace App\Business\Injector;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Site\SiteManager;

/**
 * Injector
 */
class Injector
{
	/**
	 * Object constructor
	 *
	 * @access public
	 * @param SiteManager $siteManager
	 */
	public function __construct(SiteManager $siteManager)
	{
		$this->siteManager = $siteManager;
	}

	/**
	 * Inject managers into job request class
	 *
	 * @access public
	 * @param BaseJob $job
	 * @return BaseJob
	 */
	public function inject(BaseJob $job) : BaseJob
	{
		if ($job instanceof SiteManagerAwareInterface) {
			$job->setSiteManager($this->siteManager);
		}

		return $job;
	}
}