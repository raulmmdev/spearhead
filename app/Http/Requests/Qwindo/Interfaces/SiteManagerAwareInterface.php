<?php

namespace App\Http\Requests\Qwindo\Interfaces;

use App\Business\Site\SiteManager;

interface SiteManagerAwareInterface {
	public function setSiteManager(SiteManager $siteManager);
	public function getSiteManager(): SiteManager;
}