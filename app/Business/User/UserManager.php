<?php

namespace App\Business\User;

use App\Business\Job\CreateSiteJob;
use App\Business\User\Attribute\UserAttributeManager;
use App\Model\Entity\User;

/**
 * UserManager
 */
class UserManager
{
    /**
     * $userAttributeManager
     * @access protected
     * @var $userAttributeManager
     */
    protected $userAttributeManager;

    /**
     * __construct
     * @param UserAttributeManager $userAttributeManager
     */
    public function __construct(
        UserAttributeManager $userAttributeManager
    ) {
        $this->userAttributeManager = $userAttributeManager;
    }

    /**
     * createFromSiteJob
     *
     * @access public
     * @param  CreateSiteJob $job
     * @return User
     */
    public function createFromSiteJob(CreateSiteJob $job): User
    {
        //create the user
        $user = new User();
        $user->setName($job->data['merchant']['name']);
        $user->setEmail($job->data['merchant']['email_address']);
        $user->setPassword(bin2hex(openssl_random_pseudo_bytes(4)));
        $user->setStatus(User::STATUS_ENABLED);

        $user->save();

        //create attributes
        $this
            ->userAttributeManager
            ->setUserAttribute(
                $user,
                User::ATTRIBUTE_MERCHANT_ID,
                $job->data['merchant']['merchant_id']
            );

        return $user;
    }
}
