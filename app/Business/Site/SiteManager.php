<?php

namespace App\Business\Site;

use App\Business\Job\CreateSiteJob;
use App\Model\Entity\Site;

/**
 * SiteManager
 */
class SiteManager
{
    /**
     * Create a new site from a job.
     *
     * @access public
     * @param CreateSiteJob $job
     * @return CreateSiteJob | null
     */
    public function createFromJob(CreateSiteJob $job) :? CreateSiteJob
    {
        try {
            $site = new Site();
            $site->setName($job->data['site']['name']);
            $site->save();

            $job->setObject($site);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            $job->setErrors([
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $job;
    }
}
