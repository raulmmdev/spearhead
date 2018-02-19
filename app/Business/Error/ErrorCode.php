<?php
namespace App\Business\Site;

use App\Http\Requests\SaveSite;
use App\Site;
  
class SiteManager
{
    const ERROR_SAVE_SITE = 'QWSIT0001';

    const MESSAGES        = [
        self::ERROR_SAVE_SITE = 'There was an error trying to save your site';
    ];

	/**
     * Create a new site from a Request.
     *
     * @param  SaveSite  $request
     * @return Site
     */
    public static function getMessage(string $code): string
    {
    	return self::MESSAGES[$code];
    }
}