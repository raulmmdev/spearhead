<?php

namespace App\Business\Api;

use App\Model\Entity\ApiFeature;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Site;

/**
 * ApiFeatureManager
 */
class ApiFeatureManager
{
    /**
     * $apiFeatureRepository
     * @access protected
     * @var $apiFeatureRepository
     */
    protected $apiFeatureRepository;

    /**
     * __construct
     * @param ApiFeatureRepository $apiFeatureRepository
     */
    public function __construct(
        ApiFeatureRepository $apiFeatureRepository
    ) {
        $this->apiFeatureRepository = $apiFeatureRepository;
    }

    /**
     * Create a new apiFeature from a job.
     *
     * @access public
     * @param CreateSiteJob $job
     * @return apiFeature
     */
    public function create(Site $site) :? apiFeature
    {
        //feature exists? make sure its enabled and use it
        $feature = $this->apiFeatureRepository->findByField('site_id', $site->getId())->first();
        if ($feature != null) {
            $feature->setStatus(apiFeature::STATUS_ENABLED);

            $feature->save();

            return $feature;
        }

        $feature = new apiFeature();
        $feature->site()->associate($site);
        $feature->setLogin(uniqid());
        $feature->setKey(uniqid());
        $feature->setStatus(ApiFeature::STATUS_ENABLED);

        $feature->save();

        return $feature;
    }
}
