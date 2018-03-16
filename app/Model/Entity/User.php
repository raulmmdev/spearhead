<?php

namespace App\Model\Entity;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * One user has many API features
     */
    public function apiFeatures()
    {
        return $this->hasMany('App\Model\Entity\ApiFeature', 'user_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * One user has many SiteProvider features
     */
    public function siteProviderFeatures()
    {
        return $this->hasMany('App\Model\Entity\SiteProviderFeature');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
