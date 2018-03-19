<?php

namespace App\Business\User\Attribute;

use App\Model\Entity\User;
use App\Model\Entity\UserAttribute;

/**
 * UserAttributeManager
 */
class UserAttributeManager
{

    /**
     * setUserAttribute
     *
     * @access public
     * @param User   $user
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setUserAttribute(User $user, string $name, string $value): void
    {
        $attr = new UserAttribute();
        $attr->user()->associate($user);
        $attr->setName($name);
        $attr->setValue($value);

        $attr->save();
    }
}
