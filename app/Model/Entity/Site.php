<?php

namespace App\Model\Entity;

/**
 * Site
 */
class Site extends BaseModel
{
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
    protected $fillable = ['name'];
}
