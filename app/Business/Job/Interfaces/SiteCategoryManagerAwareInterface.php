<?php

namespace App\Business\Job\Interfaces;

use App\Business\SiteCategory\SiteCategoryManager;

/**
 * SiteCategoryManagerAwareInterface
 */
interface SiteCategoryManagerAwareInterface
{
    public function setSiteCategoryManager(SiteCategoryManager $siteCategoryManager);
    public function getSiteCategoryManager() : SiteCategoryManager;
}
