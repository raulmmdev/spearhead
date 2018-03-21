<?php

namespace App\Model\Entity;

/**
 * Product
 */
class Product extends BaseProduct
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
    protected $table = 'product';

    /**
     * The attributes that are not visible
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

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

        $this->hidden = $this->base_hidden + $this->hidden;
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
        return $this->hasMany('App\Model\Entity\ProductVariant', 'product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A product belongs to many site categories
     */
    public function categories()
    {
        return $this->hasMany('App\Model\Entity\SiteCategory');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A product has many attributes
     */
    public function attributes()
    {
        return $this->hasMany('App\Model\Entity\ProductAttribute', 'product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
