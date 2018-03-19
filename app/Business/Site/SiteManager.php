<?php

namespace App\Business\Site;

use App\Business\Api\ApiFeatureManager;
use App\Business\Job\CreateSiteJob;
use App\Business\User\UserManager;
use App\Model\Entity\Site;
use App\Model\Entity\User;

/**
 * SiteManager
 */
class SiteManager
{

    /**
     * $userManager
     * @access protected
     * @var $userManager
     */
    protected $userManager;

    /**
     * $apiFeatureManager
     * @access protected
     * @var $apiFeatureManager
     */
    protected $apiFeatureManager;

    /**
     * __construct
     * @param UserManager $userManager
     */
    public function __construct(
        UserManager $userManager,
        ApiFeatureManager $apiFeatureManager
    ) {
        $this->userManager = $userManager;
        $this->apiFeatureManager = $apiFeatureManager;
    }

    /**
     * Create a new site from a job.
     *
     * @access public
     * @param CreateSiteJob $job
     * @return CreateSiteJob | null
     */
    public function createFromJob(CreateSiteJob $job) :? CreateSiteJob
    {
        \DB::beginTransaction();

        try {
            //create the user
            $user = $this->userManager->createFromSiteJob($job);
            //create the site
            $site = $this->createSiteFromJob($job, $user);
            //create the api feature
            $feature = $this->apiFeatureManager->create($site);

            $job->setObject($site);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            \DB::rollback();

            $job->setErrors([
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        \DB::commit();

        return $job;
    }

    /**
     * createSiteFromJob
     *
     * @access private
     * @param  CreateSiteJob $job
     * @return Site
     */
    private function createSiteFromJob(CreateSiteJob $job, User $user): Site
    {
        $site = new Site();
        $site->setName($job->data['site']['portal_description']);
        $site->user()->associate($user);
        $site->save();

        return $site;
    }
}
