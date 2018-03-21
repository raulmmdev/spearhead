<?php

namespace App\Business\SiteProduct;

use App\Business\Job\DeleteSiteProductJob;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Site;
use App\Model\Entity\SiteProduct;

/**
 * SiteProductManager
 */
class SiteProductManager
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ApiFeatureRepository container
     *
     * @access protected
     * @var ApiFeatureRepository
     */
    protected $apiFeatureRepository;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(ApiFeatureRepository $apiFeatureRepository)
    {
        $this->apiFeatureRepository = $apiFeatureRepository;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Delete the product from a job.
     *
     * @access public
     * @param DeleteSiteProductJob $job
     * @return DeleteSiteProductJob | null
     */
    public function deleteFromJob(DeleteSiteProductJob $job) : ?DeleteSiteProductJob
    {
        // Extract the variables from $job->data into current scope
        // $job->data['user'] ==> $user;
        // $job->data['product'] ==> $product;
        extract($job->data);

        try {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $product = SiteProduct::where('source_id', $product['id'])->first();
            $product->status = SiteProduct::STATUS_DISABLED;
            $product->save();

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $job->setObject($product);
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

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //------------------------------------------------------------------------------------------------------------------

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------

}
