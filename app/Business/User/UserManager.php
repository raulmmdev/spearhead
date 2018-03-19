<?php

namespace App\Business\User;

use App\Business\Job\CreateSiteJob;
use App\Business\User\Attribute\UserAttributeManager;
use App\Model\Entity\User;
use App\Model\Entity\Repository\UserRepository;

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
     * $userRepository
     * @access protected
     * @var $userRepository
     */
    protected $userRepository;

    /**
     * __construct
     * @param UserAttributeManager $userAttributeManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserAttributeManager $userAttributeManager,
        UserRepository $userRepository
    ) {
        $this->userAttributeManager = $userAttributeManager;
        $this->userRepository = $userRepository;
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
        //user exists? make sure its enabled and use it
        $user = $this->userRepository->findByField('email', $job->data['merchant']['email_address'])->first();
        if ($user != null) {
            $user->setStatus(User::STATUS_ENABLED);

            $user->save();

            return $user;
        }

        //create the user
        $user = new User();
        $user->setName($job->data['merchant']['name']);
        $user->setEmail($job->data['merchant']['email_address']);
        $user->setCountry($job->data['merchant']['country']);
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
