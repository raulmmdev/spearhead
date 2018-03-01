<?php

namespace App\Model\Document;

/**
 * BusinessLog
 */
class BusinessLog extends \Moloquent
{
    const LEVEL_TYPE_INFO = 'INFO';
    const LEVEL_TYPE_ERROR = 'ERROR';
    const LEVEL_TYPE_EXCEPTION = 'EXCEPTION';

    const USER_TYPE_MERCHANT = 'MERCHANT';
    const USER_TYPE_ADMIN = 'ADMIN';

    const ELEMENT_TYPE_PRODUCT = 'PRODUCT';
    const ELEMENT_TYPE_CATEGORY = 'CATEGORY';
    const ELEMENT_TYPE_SITE = 'SITE';
    const ELEMENT_TYPE_IMAGE = 'IMAGE';

    const HTTP_TYPE_PUSH = 'PUSH';
    const HTTP_TYPE_PULL = 'PULL';

    //mappings to relate log to queues
    const BUSINESS_LOG_ELEMENT_TYPES = [
        \App\Http\Requests\Qwindo\SaveSiteRequest::QUEUE => self::ELEMENT_TYPE_SITE,
    ];

    /**
     * @access protected
     * @var string
     */
    protected $collection = 'business_log';

    /**
     * @access protected
     * @var string
     */
    protected $connection = 'mongodb';
}
