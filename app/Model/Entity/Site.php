<?php

namespace App\Model\Entity;

/**
 * Site
 */
class Site extends BaseModel
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';
    const STATUS_BLOCKED = 'BLOCKED';

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'site';

    /**
     * Indicates if the model should be timestamped.
     *
     * @access public
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * One site has many categories
     */
    public function categories()
    {
        return $this->hasMany('App\Model\Entity\SiteCategory', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * One site has many products
     */
    public function products()
    {
        return $this->hasMany('App\Model\Entity\Product', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * One site has many product variants
     */
    public function variants()
    {
        return $this->hasMany('App\Model\Entity\ProductVariant', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * One user has many API features
     */
    public function apiFeatures()
    {
        return $this->hasMany('App\Model\Entity\ApiFeature', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    // MISCELANEOUS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * isEnabled
     *
     * @access public
     * @return boolean [description]
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }


    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
