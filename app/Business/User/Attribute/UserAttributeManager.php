<?php

namespace App\Business\User\Attribute;

use App\Model\Entity\Repository\UserAttributeRepository;
use App\Model\Entity\User;
use App\Model\Entity\UserAttribute;

/**
 * UserAttributeManager
 */
class UserAttributeManager
{
    /**
     * $userAttributeRepository
     * @access protected
     * @var $userAttributeRepository
     */
    protected $userAttributeRepository;

    /**
     * __construct
     * @param UserAttributeRepository $userAttributeRepository
     */
    public function __construct(
        UserAttributeRepository $userAttributeRepository
    ) {
        $this->userAttributeRepository = $userAttributeRepository;
    }

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
        //attribute exists? use it
        $attr = $this->userAttributeRepository->findWhere(['user_id' => $user->getId(), 'name' => $name])->first();
        if ($attr === null) {
            $attr = new UserAttribute();
            $attr->user()->associate($user);
            $attr->setName($name);
        }

        $attr->setValue($value);
        $attr->save();
    }
}
