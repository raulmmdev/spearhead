<?php

namespace App\Business\Job;

use App\Business\Job\BaseJob;
use App\Business\Job\Interfaces\SiteManagerAwareInterface;
use App\Business\Site\SiteManager;

/**
 * DeleteSiteJob
 */
class DeleteSiteJob extends BaseJob implements SiteManagerAwareInterface
{
    /**
     * SiteManager
     *
     * @access private
     * @var SiteManager
     */
    private $siteManager;

    /**
     * SiteManager setter
     *
     * @access public
     * @param SiteManager $siteManager
     * @return void
     */
    public function setSiteManager(SiteManager $siteManager) : void
    {
        $this->siteManager = $siteManager;
    }

    /**
     * SiteManager getter
     *
     * @access public
     * @return SiteManager
     */
    public function getSiteManager() : SiteManager
    {
        return $this->siteManager;
    }

    /**
     * Resolve the job
     *
     * @access public
     * @return CreateSiteJob | null
     */
    public function resolve() :? DeleteSiteJob
    {
        return $this->siteManager->deleteFromJob($this);
    }
}
