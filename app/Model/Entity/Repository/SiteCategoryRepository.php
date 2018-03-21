<?php

namespace App\Model\Entity\Repository;

use App\Model\Entity\SiteCategory;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Model\Entity\Site;

/**
 * SiteCategoryRepository
 */
class SiteCategoryRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @access public
     * @return string
     */
    public function model()
    {
        return 'App\Model\Entity\SiteCategory';
    }

    /**
     * Disable SiteTree
     *
     * @param  Site   $site
     * @return void
     */
    public function disableSiteTree(Site $site): void
    {
        SiteCategory::where('site_id', $site->id)->update([
            'status' => SiteCategory::STATUS_DISABLED
        ]);
    }
}
