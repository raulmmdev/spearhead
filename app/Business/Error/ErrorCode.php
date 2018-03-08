<?php

namespace App\Business\Error;

/**
 * ErrorCode
 */
class ErrorCode
{
    const ERROR_CODE_SAVE_MESSAGE = 'QWMSG0001';
    const ERROR_CODE_MISSING_WSSE = 'QW0001';
    const ERROR_CODE_INVALID_WSSE = 'QW0002';
    const ERROR_CODE_WRONG_CREDENTIALS = 'QW0003';
    const ERROR_CODE_WSSE_FAILED = 'QW0004';

    const ERROR_MESSAGES = [
        self::ERROR_CODE_SAVE_MESSAGE => 'There was an error trying to save the message',
        self::ERROR_CODE_MISSING_WSSE => 'Missing WSSE header',
        self::ERROR_CODE_INVALID_WSSE => 'Invalid WSSE header',
        self::ERROR_CODE_WRONG_CREDENTIALS => 'Invalid credentials provided',
        self::ERROR_CODE_WSSE_FAILED => 'WSSE Authentication failed',
    ];
}
