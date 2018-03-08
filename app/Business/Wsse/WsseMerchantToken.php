<?php

namespace App\Business\Wsse;

/**
 * WsseMerchantToken
 */
class WsseMerchantToken
{
    /**
     * userLogin
     *
     * @access public
     * @var string
     */
    public $userLogin;

    /**
     * digest
     *
     * @access public
     * @var string
     */
    public $digest;

    /**
     * nonce
     *
     * @access public
     * @var string
     */
    public $nonce;

    /**
     * created
     *
     * @access public
     * @var int
     */
    public $created;

    /**
     * string
     *
     * @access public
     * @var string
     */
    public $ip;
}
