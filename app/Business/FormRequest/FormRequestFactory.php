<?php

namespace App\Business\FormRequest;

use App\Business\Api\Request\ApiRequest;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Injector\Injector;
use App\Model\Document\BusinessLog;

class FormRequestFactory
{
	private $request;

	const CLASS_NAMES = [
		ApiRequest::MSG_CREATE_SITE => 'App\Http\Requests\Qwindo\SaveSite',
	];

	public function __construct(Injector $injector, BusinessLogManager $businessLogManager)
	{
		$this->injector = $injector;
		$this->businessLogManager = $businessLogManager;
	}

	public function create(string $type, array $values)
	{
		$className = self::CLASS_NAMES[$type];

		//create the request, inject the managers
		$this->request = new $className();
		$this->request = $this->injector->inject($this->request);

		//fill the instance with data
		try {
			switch($type) {
				case ApiRequest::MSG_CREATE_SITE:
					$this->fillCreateSiteRequest($values);
					break;
			}
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
			\Log::error($e->getTraceAsString());

			//business log to admin level
			$this->businessLogManager->error(
				BusinessLog::USER_TYPE_ADMIN,
				BusinessLog::BUSINESS_LOG_ELEMENT_TYPES[$type],
				"Error processing a message [ {$type} ]",
				json_encode([
					'body' => $values,
					'errors' => [
						0 => $e->getMessage(),
						1 => $e->getTraceAsString(),
					],
				])
			);

			$this->request->setErrors([
				0 => 'There was an unexpected error trying to create your request'
			]);
		}

		return $this->request;
	}

	private function fillCreateSiteRequest(array $values)
	{
		isset($values['name']) && $this->request['name'] = $values['name'];
	}
}