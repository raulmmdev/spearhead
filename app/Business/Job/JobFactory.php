<?php

namespace App\Business\Job;

use App\Business\Api\Request\ApiRequest;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Injector\Injector;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use App\Model\Document\BusinessLog;

class JobFactory
{
	private $job;

	const CLASS_NAMES = [
		SaveSiteRequest::QUEUE => 'App\Business\Job\CreateSiteJob',
	];

	public function __construct(Injector $injector, BusinessLogManager $businessLogManager)
	{
		$this->injector = $injector;
		$this->businessLogManager = $businessLogManager;
	}

	public function create(string $queue, array $values)
	{
		$className = self::CLASS_NAMES[$queue];

		//create the job, inject the managers
		$this->job = new $className();
		$this->job = $this->injector->inject($this->job);

		//fill the instance with data
		try {
			switch($queue) {
				case SaveSiteRequest::QUEUE:
					$this->fillCreateSiteJob($values);
					break;
			}
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
			\Log::error($e->getTraceAsString());

			//business log to admin level
			$this->businessLogManager->error(
				BusinessLog::USER_TYPE_ADMIN,
				BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$queue],
				"Error processing a message from queue [ {$queue} ]",
				json_encode([
					'request' => $values,
					'errors' => [
						0 => $e->getMessage(),
						1 => $e->getTraceAsString(),
					],
				])
			);

			$this->job->setErrors([
				0 => 'There was an unexpected error trying to create your job'
			]);
		}

		return $this->job;
	}

	private function fillCreateSiteJob(array $values)
	{
		isset($values['name']) && $this->job->data['name'] = $values['name'];
	}
}