<?php

namespace App\Business\Injector;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteCategoryManagerAwareInterface;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Job\Interfaces\SiteProductManagerAwareInterface;
use App\Business\Site\SiteManager;
use App\Business\SiteCategory\SiteCategoryManager;
use App\Business\SiteProduct\SiteProductManager;

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
    public function __construct(
        SiteManager $siteManager,
        SiteCategoryManager $siteCategoryManager,
        SiteProductManager $siteProductManager
    )
    {
        $this->siteManager = $siteManager;
        $this->siteCategoryManager = $siteCategoryManager;
        $this->siteProductManager = $siteProductManager;
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

        if ($job instanceof SiteProductManagerAwareInterface) {
            $job->setSiteProductManager($this->siteProductManager);
        }

        return $job;
    }
}
