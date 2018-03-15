<?php

namespace App\Business\Injector;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteCategoryManagerAwareInterface;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Site\SiteManager;
use App\Business\SiteCategory\SiteCategoryManager;

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
    public function __construct(SiteManager $siteManager, SiteCategoryManager $siteCategoryManager)
    {
        $this->siteManager = $siteManager;
        $this->siteCategoryManager = $siteCategoryManager;
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

        if ($job instanceof SiteCategoryManagerAwareInterface) {
            $job->setSiteCategoryManager($this->siteCategoryManager);
        }

        return $job;
    }
}
