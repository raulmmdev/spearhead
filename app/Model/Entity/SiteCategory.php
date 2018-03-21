<?php

namespace App\Model\Entity;

/**
 * SiteCategory
 */
class SiteCategory extends BaseModel
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'site_category';

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
        'source_id',
        'parent_id',
        'title',
        'cashback',
        'status',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * A category belongs to a site
     */
    public function site()
    {
        return $this->belongsTo('App\Model\Entity\Site', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * A category has many products
     */
    public function products()
    {
        return $this->belongsToMany('App\Model\Entity\Product');
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
