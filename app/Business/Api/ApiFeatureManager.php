<?php

namespace App\Business\Api;

use App\Model\Entity\ApiFeature;
use App\Model\Entity\Site;

/**
 * ApiFeatureManager
 */
class ApiFeatureManager
{
    /**
     * Create a new apiFeature from a job.
     *
     * @access public
     * @param CreateSiteJob $job
     * @return apiFeature
     */
    public function create(Site $site) :? apiFeature
    {
        $feature = new apiFeature();
        $feature->site()->associate($site);
        $feature->setLogin(uniqid());
        $feature->setKey(uniqid());
        $feature->setStatus(ApiFeature::STATUS_ENABLED);

        $feature->save();

        return $feature;
    }
}
