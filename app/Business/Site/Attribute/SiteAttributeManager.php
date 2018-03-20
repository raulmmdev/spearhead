<?php

namespace App\Business\Site\Attribute;

use App\Model\Entity\Site;
use App\Model\Entity\SiteAttribute;
use App\Model\Entity\Repository\SiteAttributeRepository;

/**
 * SiteAttributeManager
 */
class SiteAttributeManager
{
    /**
     * $siteAttributeRepository
     * @access protected
     * @var $siteAttributeRepository
     */
    protected $siteAttributeRepository;

    /**
     * __construct
     * @param UserAttributeRepository $siteAttributeRepository
     */
    public function __construct(
        SiteAttributeRepository $siteAttributeRepository
    ) {
        $this->siteAttributeRepository = $siteAttributeRepository;
    }

    /**
     * setSiteAttribute
     *
     * @access public
     * @param Site   $site
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setSiteAttribute(Site $site, string $name, string $value = null): void
    {
        //attribute exists? use it
        $attr = $this->siteAttributeRepository->findWhere(['site_id' => $site->getId(), 'name' => $name])->first();
        if ($attr === null) {
            $attr = new SiteAttribute();
            $attr->site()->associate($site);
            $attr->setName($name);
        }

        $attr->setValue($value);
        $attr->save();
    }
}
