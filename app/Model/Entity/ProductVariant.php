<?php

namespace App\Model\Entity;

/**
 * ProductVariant
 */
class ProductVariant extends BaseProduct
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
    protected $table = 'product_variant';

    /**
     * The attributes that are not visible
     *
     * @var array
     */
    protected $hidden = [
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
     * A product variant belongs to a product
     */
    public function product()
    {
        return $this->belongsTo('App\Model\Entity\Product', 'product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
