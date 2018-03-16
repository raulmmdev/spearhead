<?php
namespace App\Model\Entity\Repository;

use Prettus\Repository\Eloquent\BaseRepository;

/**
 * SiteProviderFeatureRepository
 */
class SiteProviderFeatureRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @access public
     * @return string
     */
    public function model()
    {
        return 'App\Model\Entity\SiteProviderFeature';
    }
}
