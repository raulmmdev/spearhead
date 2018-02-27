<?php

namespace App\Business\FormRequest;

use App\Business\Api\Request\ApiRequest;
use App\Business\Injector\Injector;

class FormRequestFactory
{
	private $request;

	const CLASS_NAMES = [
		ApiRequest::MSG_CREATE_SITE => 'App\Http\Requests\Qwindo\SaveSite',
	];

	public function __construct(Injector $injector)
	{
		$this->injector = $injector;
	}

	public function create(string $type, array $values)
	{
		$className = self::CLASS_NAMES[$type];

		//create the request, inject the managers
		$this->request = new $className();
		$this->request = $this->injector->inject($this->request);

		//fill the instance with data
		switch($type) {
			case ApiRequest::MSG_CREATE_SITE:
				$this->fillCreateSiteRequest($values);
				break;
		}

		return $this->request;
	}

	private function fillCreateSiteRequest(array $values)
	{
		isset($values['name']) && $this->request['name'] = $values['name'];
	}
}