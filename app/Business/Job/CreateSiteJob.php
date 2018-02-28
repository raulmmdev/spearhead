<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Site\SiteManager;

class CreateSiteJob extends BaseJob implements SiteManagerAwareInterface
{
	private $siteManager;

	public $data = [];

	public function setSiteManager(SiteManager $siteManager)
	{
		$this->siteManager = $siteManager;
	}

	public function getSiteManager(): SiteManager
	{
		return $this->siteManager;
	}

	public function resolve()
	{
		return $this->siteManager->createFromJob($this);
	}
}