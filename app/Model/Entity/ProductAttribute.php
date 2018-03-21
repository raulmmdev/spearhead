<?php

namespace App\Model\Entity;

/**
 * Product
 */
class ProductAttribute extends BaseModel
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const ATTRIBUTE_BRAND = 'BRAND';
    const ATTRIBUTE_IS_DOWNLOADABLE = 'IS_DOWNLOADABLE';
    const ATTRIBUTE_LONG_DESCRIPTION = 'LONG_DESCRIPTION';
    const ATTRIBUTE_METADATA = 'METADATA';
    const ATTRIBUTE_SHORT_DESCRIPTION = 'SHORT_DESCRIPTION';
    const ATTRIBUTE_TAX = 'TAX';

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'product_attribute';

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
        'name',
        'value',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * A product has many variants
     */
    public function product()
    {
        return $this->belongsTo('App\Model\Entity\Product', 'product_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
