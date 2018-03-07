<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * ApiFeature
 */
class ApiFeature extends Model
{
    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'api_feature';

    /**
     * Indicates if the model should be timestamped.
     *
     * @access public
     * @var bool
     */
    public $timestamps = true;

    /**
     * DB RELATIONSHIPS
     */

    /**
     * An API feature belongs to one User
     */
    public function user()
    {
        return $this->belongsTo('App\Model\Entity\User', 'user_id', 'id');
    }

    //getters and setters
    public function setUserId(int $id): void
    {
        $this->attributes['user_id'] = $id;
    }
}
