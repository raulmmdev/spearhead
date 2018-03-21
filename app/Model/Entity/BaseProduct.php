<?php

namespace App\Model\Entity;

/**
 * BaseProduct
 */
class BaseProduct extends BaseModel
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';

    /**
     * Indicates if the model should be timestamped.
     *
     * @access public
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are not visible
     *
     * @var array
     */
    protected $base_hidden = [
    ];

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $base_fillable = [
        'source_id',
        'sku_number',
        'gtin',
        'name',
        'images',
        'sale_price',
        'retail_price',
        'stock',
        'cashback',
        'status',
        'weight',
        'custom_attributes',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * A product belongs to a site
     */
    public function site()
    {
        return $this->belongsTo('App\Model\Entity\Site', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    // MISCELANEOUS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * isEnabled
     *
     * @access public
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
