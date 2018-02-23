<?php
namespace App\Business\Error;

use App\Http\Requests\SaveSite;
use App\Site;
  
class ErrorCode
{
    const ERROR_SAVE_MESSAGE = 'QWMSG0001';

    const MESSAGES        = [
        self::ERROR_SAVE_MESSAGE => 'There was an error trying to save the message',
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