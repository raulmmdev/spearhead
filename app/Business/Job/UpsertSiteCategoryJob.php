<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteCategoryManagerAwareInterface;
use App\Business\SiteCategory\SiteCategoryManager;

/**
 * UpsertSiteCategoryJob
 */
class UpsertSiteCategoryJob extends BaseJob implements SiteCategoryManagerAwareInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * CategoryManager
     *
     * @access private
     * @var CategoryManager
     */
    private $siteCategoryManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * SiteManager setter
     *
     * @access public
     * @param SiteCategoryManager $siteCategoryManager
     * @return void
     */
    public function setSiteCategoryManager(SiteCategoryManager $siteCategoryManager) : void
    {
        $this->siteCategoryManager = $siteCategoryManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * SiteManager getter
     *
     * @access public
     * @return SiteCategoryManager
     */
    public function getSiteCategoryManager() : SiteCategoryManager
    {
        return $this->siteCategoryManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Resolve current job
     *
     * @access public
     * @return UpsertSiteCategoryJob | null
     */
    public function resolve() : ?UpsertSiteCategoryJob
    {
        return $this->siteCategoryManager->upsertFromJob($this);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
