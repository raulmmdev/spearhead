<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteProductManagerAwareInterface;
use App\Business\SiteProduct\SiteProductManager;

/**
 * DeleteSiteProductJob
 */
class DeleteSiteProductJob extends BaseJob implements SiteProductManagerAwareInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * CategoryManager
     *
     * @access private
     * @var SiteProductManager
     */
    private $siteProductManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * SiteProductManager setter
     *
     * @access public
     * @param SiteProductManager $siteProductManager
     * @return void
     */
    public function setSiteProductManager(SiteProductManager $siteProductManager) : void
    {
        $this->siteProductManager = $siteProductManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * SiteProductManager getter
     *
     * @access public
     * @return SiteProductManager
     */
    public function getSiteProductManager() : SiteProductManager
    {
        return $this->siteProductManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Resolve current job
     *
     * @access public
     * @return DeleteSiteCategoryJob | null
     */
    public function resolve() : ?DeleteSiteProductJob
    {
        return $this->siteProductManager->deleteFromJob($this);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
