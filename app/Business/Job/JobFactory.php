<?php

namespace App\Business\Job;

use App\Business\Api\Request\ApiRequest;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Injector\Injector;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use App\Model\Document\BusinessLog;

/**
 * JobFactory
 */
class JobFactory
{
	const CLASS_NAMES = [
		SaveSiteRequest::QUEUE => 'App\Business\Job\CreateSiteJob',
	];

	/**
	 * Job entity
	 *
	 * @access private
	 * @var BaseJob
	 */
	private $job;

	/**
	 * Object constructor
	 *
	 * @access public
	 * @param Injector           $injector
	 * @param BusinessLogManager $businessLogManager
	 * @return void
	 */
	public function __construct(Injector $injector, BusinessLogManager $businessLogManager)
	{
		$this->injector = $injector;
		$this->businessLogManager = $businessLogManager;
	}

	/**
	 * Create an instance of specific job
	 *
	 * @access public
	 * @param  string $queue
	 * @param  array  $values
	 * @return BaseJob
	 */
	public function create(string $queue, array $values) : BaseJob
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

	/**
	 * Fills the CreateSiteJob instance data container
	 *
	 * @access private
	 * @param  array  $values
	 * @return void
	 */
	private function fillCreateSiteJob(array $values) : void
	{
		isset($values['name']) && $this->job->data['name'] = $values['name'];
	}
}