<?php

namespace App\Business\BusinessLog;

use App\Model\Document\BusinessLog;

/**
 * BusinessLogManager
 */
class BusinessLogManager
{
	/**
	 * info
	 *
	 * @param  string      $userType
	 * @param  string      $elementType
	 * @param  string      $title
	 * @param  string      $message
	 * @param  string|null $httpType
	 * @param  int|null    $siteId
	 * @param  int|null    $feedId
	 * @return void
	 */
	public function info(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) : void {
		$this->log(
			BusinessLog::LEVEL_TYPE_INFO,
			$userType,
			$elementType,
			$title,
			$message,
			$httpType,
			$siteId,
			$feedId
		);
	}

	/**
	 * error
	 *
	 * @param  string      $userType
	 * @param  string      $elementType
	 * @param  string      $title
	 * @param  string      $message
	 * @param  string|null $httpType
	 * @param  int|null    $siteId
	 * @param  int|null    $feedId
	 * @return void
	 */
	public function error(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) : void {
		$this->log(
			BusinessLog::LEVEL_TYPE_ERROR,
			$userType,
			$elementType,
			$title,
			$message,
			$httpType,
			$siteId,
			$feedId
		);
	}

	/**
	 * exception
	 *
	 * @param  string      $userType
	 * @param  string      $elementType
	 * @param  string      $title
	 * @param  string      $message
	 * @param  string|null $httpType
	 * @param  int|null    $siteId
	 * @param  int|null    $feedId
	 * @return void
	 */
	public function exception(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) : void {
		$this->log(
			BusinessLog::LEVEL_TYPE_EXCEPTION,
			$userType,
			$elementType,
			$title,
			$message,
			$httpType,
			$siteId,
			$feedId
		);
	}

	/**
	 * Standard log
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