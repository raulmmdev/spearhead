<?php

namespace App\Model\Entity;

/**
 * SiteProductVariant
 */
class SiteProductVariant extends BaseProduct
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
    protected $table = 'site_product_variant';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
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
     * A product variant belongs to a site_product
     */
    public function product()
    {
        return $this->belongsTo('App\Model\Entity\SiteProduct', 'site_product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
