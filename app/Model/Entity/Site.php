<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * Site
 */
class Site extends Model
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
}
