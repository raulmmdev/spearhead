<?php

namespace App\Model\Document;

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

	protected $collection = 'business_log';
    protected $connection = 'mongodb';
}
