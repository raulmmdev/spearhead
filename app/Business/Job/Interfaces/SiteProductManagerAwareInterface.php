<?php

namespace App\Business\Job\Interfaces;

use App\Business\SiteProduct\SiteProductManager;

/**
 * SiteProductManagerAwareInterface
 */
interface SiteProductManagerAwareInterface
{
    public function setSiteProductManager(SiteProductManager $siteProductManager);
    public function getSiteProductManager() : SiteProductManager;
}
