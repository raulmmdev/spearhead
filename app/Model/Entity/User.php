<?php

namespace App\Model\Entity;

use Illuminate\Notifications\Notifiable;

class User extends BaseModel
{
    use Notifiable;

    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';

    const ATTRIBUTE_MERCHANT_ID = 'MERCHANT_ID';

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
        'name', 'email', 'password', 'country', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * One user has many SiteProvider features
     */
    public function siteProviderFeatures()
    {
        return $this->hasMany('App\Model\Entity\SiteProviderFeature');
    }

    /**
     * One user has many attributes
     */
    public function attributes()
    {
        return $this->hasMany('App\Model\Entity\UserAttribute');
    }

    /**
     * One user has many sites
     */
    public function sites()
    {
        return $this->hasMany('App\Model\Entity\Site');
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
