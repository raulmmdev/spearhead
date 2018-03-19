<?php

namespace App\Model\Entity;

/**
 * SiteProduct
 */
class SiteProduct extends BaseProduct
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'site_product';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'tax',
        'brand',
        'short_description',
        'long_description',
        'metadata',
        'is_downloadable',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object constructor
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->fillable = $this->base_fillable + $this->fillable;
    }

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * A product has many variants
     */
    public function variants()
    {
        return $this->hasMany('App\Model\Entity\SiteProductVariant', 'site_product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
