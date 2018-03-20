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

        //TODO:this is marcins crap, we should change it in the future
        $feature->setLogin(
            substr(
                base64_encode(
                    hash_hmac(
                        'sha256',
                        trim($site->getId()).'qwindo_hash_id'.date('Y-m-d H:i:s').trim($site->getApiKey()),
                        config('qwindo.system')['salt']
                    )
                ),
                0,
                32
            )
        );

        $feature->setKey(
            substr(
                base64_encode(
                    hash_hmac(
                        'sha256',
                        trim($site->getApiKey()).'qwindo_api_key'.microtime(true).rand().trim($site->getId()),
                        config('qwindo.system')['salt']
                    )
                ),
                0,
                32
            )
        );

        $feature->setStatus(ApiFeature::STATUS_ENABLED);

        $feature->save();

        return $feature;
    }
}
