<?php

namespace App\Business\Error;

/**
 * ErrorCode
 */
class ErrorCode
{
    const ERROR_CODE_SAVE_MESSAGE = 'QWMSG0001';

    const ERROR_MESSAGES = [
        self::ERROR_CODE_SAVE_MESSAGE => 'There was an error trying to save the message',
    ];
}
