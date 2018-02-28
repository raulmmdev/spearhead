<?php

namespace App\Business\Site;

use App\Business\Job\CreateSiteJob;
use App\Model\Entity\Site;

/**
 * SiteManager
 */
class SiteManager
{
	/**
	 * Create a new site from a job.
	 *
	 * @access public
	 * @param CreateSiteJob $job
	 * @return Site | null
	 */
	public function createFromJob(CreateSiteJob $job) : ?Site
	{
		try {
			$site = new Site();
			$site->name = $job->data['name'];
			$site->save();

			return $site;
		} catch(\Exception $e) {
			\Log::error($e->getMessage());
			\Log::error($e->getTraceAsString());

			return null;
		}
	}
}