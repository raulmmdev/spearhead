<?php

namespace App\Business\BusinessLog;

use App\Model\Document\BusinessLog;

class BusinessLogManager
{
	public function info(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) {
		$this->createLog(
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

	public function error(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) {
		$this->createLog(
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

	public function exception(
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) {
		$this->createLog(
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
     * Creates a generic log message
     */
    private function createLog(
    	string $levelType,
		string $userType,
		string $elementType,
		string $title,
		string $message = '',
		string $httpType = null,
		int $siteId = null,
		int $feedId = null
	) {
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