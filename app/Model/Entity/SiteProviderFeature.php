<?php

namespace App\Model\Entity;

use App\Business\User\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * SiteProviderFeature
 */
class SiteProviderFeature extends Model implements UserInterface, Authenticatable
{
    const STATUS_ENABLED = 'ENABLED';
    const STATUS_DISABLED = 'DISABLED';
    const STATUS_BLOCKED = 'BLOCKED';

    /**
     * Indicates if the model should be timestamped.
     *
     * @access public
     * @var bool
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @access protected
     * @var string
     */
    protected $table = 'site_provider_feature';


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

    /**
     * setUserId
     * @param int $id
     */
    public function setUserId(int $id): void
    {
        $this->attributes['user_id'] = $id;
    }

    /**
     * isEnabled
     * @return boolean [description]
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }

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
}
