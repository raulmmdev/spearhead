<?php

namespace App\Business\Job\Interfaces;

use App\Business\Site\SiteManager;

interface SiteManagerAwareInterface {
	public function setSiteManager(SiteManager $siteManager);
	public function getSiteManager(): SiteManager;
}