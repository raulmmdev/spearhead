<?php

namespace App\Business\SiteCategory;

use App\Business\Job\UpsertSiteCategoryJob;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Repository\SiteRepository;
use App\Model\Entity\Site;
use App\Model\Entity\SiteCategory;
use App\Model\Entity\Repository\SiteCategoryRepository;

/**
 * SiteCategoryManager
 */
class SiteCategoryManager
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

    /**
     * SiteCategoryRepository container
     *
     * @access protected
     * @var siteCategoryRepository
     */
    protected $siteCategoryRepository;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(
        ApiFeatureRepository $apiFeatureRepository,
        SiteCategoryRepository $siteCategoryRepository
    ) {
        $this->apiFeatureRepository = $apiFeatureRepository;
        $this->siteCategoryRepository = $siteCategoryRepository;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Upsert the category tree from a job.
     *
     * @access public
     * @param UpsertSiteCategoryJob $job
     * @return UpsertSiteCategoryJob | null
     */
    public function upsertFromJob(UpsertSiteCategoryJob $job) :? UpsertSiteCategoryJob
    {
        try {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Get the site, clear the current category tree

            $site = $this->apiFeatureRepository->find($job->data['user']['id'])->site;

            $this->disableSiteCategoryTree($site);

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Load new tree

            if (is_array($job->data['tree']) && count($job->data['tree'])) {
                foreach ($job->data['tree'] as $entry) {
                    $category = $this->processEntry($site, $entry, $parentId = null);
                }
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $job->setObject($category);
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

    /**
     * Disable SiteCategoryTree
     *
     * @param  Site   $site
     * @return void
     */
    public function disableSiteCategoryTree(Site $site) : void
    {
        $this->siteCategoryRepository->disableSiteTree($site);
    }

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Recursively process each JSON entry
     *
     * @access private
     * @param  Site $site
     * @param  array $entry
     * @param  integer $parentId
     * @return SiteCategory
     */
    private function processEntry(Site $site, array $entry, $parentId) : SiteCategory
    {
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Exist current tree?

        $category = $this->siteCategoryRepository->findWhere([
            'site_id' => $site->id,
            'source_id' => $entry['id'],
        ])->first();

        if ($category !== null) {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Enable category/child if proceed

            $category->title = json_encode($entry['title']);
            $category->cashback = $entry['cashback'] ?? config('qwindo.cashback.category');
            $category->status = SiteCategory::STATUS_ENABLED;
            $category->save();
        } else {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Create the parent category

            $category = new SiteCategory();
            $category->source_id = $entry['id'];
            $category->parent_id = $parentId;
            $category->title = json_encode($entry['title']);
            $category->cashback = $entry['cashback'] ?? config('qwindo.cashback.category');
            $category->status = $entry['status'] ?? SiteCategory::STATUS_ENABLED;
            $category->site()->associate($site);
            $category->save();
        }

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Has children?

        if (isset($entry['children']) && is_array($entry['children']) && count($entry['children'])) {
            foreach ($entry['children'] as $child) {
                $this->processEntry($site, $child, $category->id);
            }
        }

        return $category;
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
