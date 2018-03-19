<?php

namespace App\Model\Entity;

class UserAttribute extends BaseModel
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'user_attribute';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'value'
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * One user attribute belongs to a user
     */
    public function user()
    {
        return $this->belongsTo('App\Model\Entity\User', 'user_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
