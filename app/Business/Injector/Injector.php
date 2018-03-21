<?php

namespace App\Business\Injector;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\ProductManagerAwareInterface;
use App\Business\Job\Interfaces\SiteCategoryManagerAwareInterface;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Product\ProductManager;
use App\Business\Site\SiteManager;
use App\Business\SiteCategory\SiteCategoryManager;

/**
 * Injector
 */
class Injector
{
    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object constructor
     *
     * @access public
     * @param SiteManager $siteManager
     */
    public function __construct(
        SiteManager $siteManager,
        SiteCategoryManager $siteCategoryManager,
        ProductManager $productManager
    ) {
        $this->siteManager = $siteManager;
        $this->siteCategoryManager = $siteCategoryManager;
        $this->productManager = $productManager;
    }

    //------------------------------------------------------------------------------------------------------------------

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

        if ($job instanceof ProductManagerAwareInterface) {
            $job->setProductManager($this->productManager);
        }

        return $job;
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
