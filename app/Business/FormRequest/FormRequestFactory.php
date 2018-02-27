<?php
namespace App\Business\FormRequest;

use App\Business\Api\Request\ApiRequest;
use App\Business\Injector\Injector;

class FormRequestFactory
{
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
		$request = new $className();
		$request = $this->injector->inject($request);

		//fill the instance with data
		switch($type) {
			case ApiRequest::MSG_CREATE_SITE:
				isset($values['name']) && $request['name'] = $values['name'];
				break;
		}

		return $request;
	}
}