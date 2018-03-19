<?php

namespace App\Model\Entity;

use App\Business\User\Interfaces\UserInterface;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * ApiFeature
 */
class ApiFeature extends BaseModel implements UserInterface, Authenticatable
{
    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';
    const STATUS_BLOCKED = 'BLOCKED';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id', 'login', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'key',
    ];

    //------------------------------------------------------------------------------------------------------------------
    // RELATIONSHIPS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * An API feature belongs to one Site
     */
    public function site()
    {
        return $this->belongsTo('App\Model\Entity\Site', 'site_id', 'id');
    }

    //------------------------------------------------------------------------------------------------------------------
    // MISCELANEOUS
    //------------------------------------------------------------------------------------------------------------------

    //getters and setters
    public function setUserId(int $id): void
    {
        $this->attributes['user_id'] = $id;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * isEnabled
     * @return boolean [description]
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }

    //------------------------------------------------------------------------------------------------------------------
    // EXTENDING AUTHENTICATABLE
    //------------------------------------------------------------------------------------------------------------------

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifierName()
     */
    public function getAuthIdentifierName()
    {
        return "login";
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifier()
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthPassword()
     */
    public function getAuthPassword()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberToken()
     */
    public function getRememberToken()
    {
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::setRememberToken()
     */
    public function setRememberToken($value)
    {
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberTokenName()
     */
    public function getRememberTokenName()
    {
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
