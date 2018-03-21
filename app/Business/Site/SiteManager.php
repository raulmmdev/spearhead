<?php

namespace App\Business\Site;

use App\Business\Api\ApiFeatureManager;
use App\Business\Job\CreateSiteJob;
use App\Business\Job\DeleteSiteJob;
use App\Business\User\UserManager;
use App\Business\Site\Attribute\SiteAttributeManager;
use App\Model\Entity\Repository\SiteRepository;
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
     * $siteRepository
     * @access protected
     * @var $siteRepository
     */
    protected $siteRepository;

    /**
     * $siteAttributeManager
     * @access protected
     * @var $siteAttributeManager
     */
    protected $siteAttributeManager;

    /**
     * __construct
     * @param UserManager          $userManager
     * @param ApiFeatureManager    $apiFeatureManager
     * @param SiteRepository       $siteRepository
     * @param SiteAttributeManager $siteAttributeManager
     */
    public function __construct(
        UserManager $userManager,
        ApiFeatureManager $apiFeatureManager,
        SiteRepository $siteRepository,
        SiteAttributeManager $siteAttributeManager
    ) {
        $this->userManager = $userManager;
        $this->apiFeatureManager = $apiFeatureManager;
        $this->siteRepository = $siteRepository;
        $this->siteAttributeManager = $siteAttributeManager;
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
            //create all the components
            $user = $this->userManager->createFromSiteJob($job);
            $site = $this->createSiteFromJob($job, $user);
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
     * Delete a site from a job.
     *
     * @access public
     * @param DeleteSiteJob $job
     * @return DeleteSiteJob | null
     */
    public function deleteFromJob(DeleteSiteJob $job) :? DeleteSiteJob
    {
        \DB::beginTransaction();
        try {
            //disable the site and all its associated api features
            $site = $this->siteRepository->findWhere(['native_id' => $job->data['site_id']])->first();
            $site->setStatus(Site::STATUS_DISABLED);

            $this->apiFeatureManager->disableSiteFeatures($site);

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
        //site exists? make sure its enabled and use it
        $site = $this->siteRepository->findByField('native_id', $job->data['site']['site_id'])->first();
        if ($site != null) {
            $site->setStatus(Site::STATUS_ENABLED);

            $site->save();

            return $site;
        }

        $site = new Site();
        $site->setName($job->data['site']['portal_description']);
        $site->setUrl($job->data['site']['portal_url']);
        $site->setApiKey($job->data['site']['site_apikey']);
        $site->setNativeId($job->data['site']['site_id']);
        $site->setStatus(Site::STATUS_ENABLED);
        $site->user()->associate($user);

        $site->save();

        //create attributes
        $this
            ->siteAttributeManager
            ->setSiteAttribute(
                $site,
                Site::ATTRIBUTE_PAYMENT_METHODS,
                json_encode($job->data['site']['portal_payment_methods'])
            );

        $this
            ->siteAttributeManager
            ->setSiteAttribute(
                $site,
                Site::ATTRIBUTE_SUPPORT_EMAIL,
                $job->data['site']['supportemail']
            );

        $this
            ->siteAttributeManager
            ->setSiteAttribute(
                $site,
                Site::ATTRIBUTE_SUPPORT_PHONE,
                $job->data['site']['supportphone']
            );

        $this
            ->siteAttributeManager
            ->setSiteAttribute(
                $site,
                Site::ATTRIBUTE_CA_CODE,
                $job->data['site']['ca_code']
            );

        $this
            ->siteAttributeManager
            ->setSiteAttribute(
                $site,
                Site::ATTRIBUTE_MCC_CODE,
                $job->data['site']['mcc']
            );

        return $site;
    }
}
