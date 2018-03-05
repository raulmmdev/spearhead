<?php

namespace App\Business\BusinessLog;

use App\Model\Document\BusinessLog;

/**
 * BusinessLogManager
 */
class BusinessLogManager
{
    /**
     * Map with [(method name = level name) => proper level] constants
     *
     * @var array
     */
    const MESSAGE_LEVELS = [
        'info' => BusinessLog::LEVEL_TYPE_INFO,
        'error' => BusinessLog::LEVEL_TYPE_ERROR,
        'exception' => BusinessLog::LEVEL_TYPE_EXCEPTION,
    ];

    /**
     * Call method
     *
     * Example: 
     *      $businessLogManager->info(
     *          BusinessLog::USER_TYPE_MERCHANT, 
     *          BusinessLog::ELEMENT_TYPE_SITE, 
     *          'Site creation request has been received.', 
     *          json_decode(json_encode($request->all()), 
     *          BusinessLog::HTTP_TYPE_PUSH
     *      );
     *
     * @access public
     * @param  string      $userType
     * @param  string      $elementType
     * @param  string      $title
     * @param  string      $message
     * @param  string|null $httpType
     * @param  int|null    $siteId
     * @param  int|null    $feedId
     * @throws \Exception  Method not implemented
     */
    public function __call($method, $arguments) {
        if (!array_key_exists($method, self::MESSAGE_LEVELS)) {
            throw new \Exception('Method [ '. $method .' ] not implemented in class [ '. __CLASS__ .' ]', 1);
        }

        array_unshift($arguments, self::MESSAGE_LEVELS[$method]);

        return call_user_func_array([__CLASS__, 'log'], $arguments);
    }

    /**
     * Call static method
     *
     * Example: 
     *      BusinessLogManager::info(
     *          BusinessLog::USER_TYPE_MERCHANT, 
     *          BusinessLog::ELEMENT_TYPE_SITE, 
     *          'Site creation request has been received.', 
     *          json_decode(json_encode($request->all()), 
     *          BusinessLog::HTTP_TYPE_PUSH
     *      );
     *
     * @static
     * @access public
     * @param  string      $userType
     * @param  string      $elementType
     * @param  string      $title
     * @param  string      $message
     * @param  string|null $httpType
     * @param  int|null    $siteId
     * @param  int|null    $feedId
     * @throws \Exception  Method not implemented
     */
    public static function __callStatic($method, $arguments) {
        if (!array_key_exists($method, self::MESSAGE_LEVELS)) {
            throw new \Exception('Method [ '. $method .' ] not implemented in class [ '. __CLASS__ .' ]', 1);
        }

        array_unshift($arguments, self::MESSAGE_LEVELS[$method]);

        return call_user_func_array([__CLASS__, 'log'], $arguments);
    }

    /**
     * Standard log method
     *
     * @param  string      $levelType
     * @param  string      $userType
     * @param  string      $elementType
     * @param  string      $title
     * @param  string      $message
     * @param  string|null $httpType
     * @param  int|null    $siteId
     * @param  int|null    $feedId
     * @return void
     */
    public function log(
        string $levelType,
        string $userType,
        string $elementType,
        string $title,
        string $message = '',
        string $httpType = null,
        int $siteId = null,
        int $feedId = null
    ) : void {
        try {
            $businessLog = new BusinessLog();
            $businessLog->id = uniqid();
            $businessLog->site_id = $siteId;
            $businessLog->feed_id = $feedId;
            $businessLog->level_type = $levelType;
            $businessLog->http_type = $httpType;
            $businessLog->user_type = $userType;
            $businessLog->element_type = $elementType;
            $businessLog->title = $title;
            $businessLog->message = $message;
            $businessLog->save();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
        }
    }
}
