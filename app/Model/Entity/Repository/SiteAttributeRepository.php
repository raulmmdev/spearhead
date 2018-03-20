<?php

namespace App\Model\Entity\Repository;

use Prettus\Repository\Eloquent\BaseRepository;

/**
 * SiteAttributeRepository
 */
class SiteAttributeRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @access public
     * @return string
     */
    public function model()
    {
        return 'App\Model\Entity\SiteAttribute';
    }
}
