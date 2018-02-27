<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
	private $errors = [];

	public function resolveIfValid()
	{
		$validator = \Validator::make($this->all(), $this->rules());

		if ($validator->passes()) {
			return $this->resolve();
		}

		$this->setErrors(json_decode(json_encode($validator->errors()), true));
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors(array $errors)
	{
		$this->errors = $errors;
	}

	abstract protected function resolve();
}