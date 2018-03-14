<?php

namespace App\Model\Entity;

use App\Business\User\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * SiteProviderFeature
 */
class SiteProviderFeature extends Model implements UserInterface
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
    protected $table = 'site_provider_feature';

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

    /**
     * isEnabled
     * @return boolean [description]
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }
}
